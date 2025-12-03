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
        $sql = "INSERT INTO recuperacao_senha (usuario_id, token, expiracao, utilizado) VALUES (:usuario_id, :token, :expiracao, 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':usuario_id' => $usuarioId, ':token' => $token, ':expiracao' => $expiracao]);
        return (int) $pdo->lastInsertId();
    }

    public static function buscarPorToken(string $token) {
        $pdo = self::getPDO();
        $sql = "SELECT * FROM recuperacao_senha WHERE token = :token AND utilizado = 0 LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':token' => $token]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    public static function marcarUtilizado(string $token): bool {
        $pdo = self::getPDO();
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
}

?>
