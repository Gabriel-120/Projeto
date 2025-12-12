<?php
/**
 * RecuperacaoModel.php - gerenciamento de tokens de recuperação (app/models)
 */
require_once __DIR__ . '/Connect.php';

class RecuperacaoModel {
    private static function getPDO() {
        return Connect::conectar();
    }

    public static function gerarToken(): string {
        return bin2hex(random_bytes(32));
    }

    public static function criarToken(int $usuarioId, string $token, string $expiracao): int {
        $pdo = self::getPDO();
        // garantir que a tabela exista (ajuda em ambientes de dev onde o script SQL não foi executado)
        self::ensureTableExists();
        $sql = "INSERT INTO recuperacao_senha (usuario_id, token, expiracao, utilizado) VALUES (:usuario_id, :token, :expiracao, 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':usuario_id' => $usuarioId, ':token' => $token, ':expiracao' => $expiracao]);
        return (int) $pdo->lastInsertId();
    }

    public static function buscarPorToken(string $token) {
        $pdo = self::getPDO();
        self::ensureTableExists();
        $sql = "SELECT * FROM recuperacao_senha WHERE token = :token AND utilizado = 0 LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':token' => $token]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    public static function marcarUtilizado(string $token): bool {
        $pdo = self::getPDO();
        self::ensureTableExists();
        $sql = "UPDATE recuperacao_senha SET utilizado = 1 WHERE token = :token";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([':token' => $token]);
    }

    public static function tokenValido(string $token): bool {
        $dados = self::buscarPorToken($token);
        if (!$dados) return false;
        $exp = strtotime($dados['expiracao']);
        return time() <= $exp;
    }

    private static function ensureTableExists(): void {
        $pdo = self::getPDO();
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS recuperacao_senha (
  id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  token VARCHAR(128) NOT NULL UNIQUE,
  expiracao DATETIME NOT NULL,
  utilizado TINYINT(1) NOT NULL DEFAULT 0,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES Usuarios(id_usuario)
);
SQL;
        $pdo->exec($sql);
    }
}

?>
