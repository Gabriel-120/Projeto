<?php
/**
 * CadastroModel.php - estilo do outro desenvolvedor (app/models)
 * Responsável por registrar usuário em `Usuarios` e em `Alunos` ou `Funcionarios`.
 */
require_once __DIR__ . '/Connect.php';

class CadastroModel {
    private static function getPDO() {
        return Connect::conectar();
    }

    public static function emailExists(string $email): bool {
        $pdo = self::getPDO();
        $sql = "SELECT 1 FROM Usuarios WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return (bool) $stmt->fetchColumn();
    }

    public static function cpfExists(string $cpf): bool {
        $pdo = self::getPDO();
        $sql = "SELECT 1 FROM Usuarios WHERE cpf = :cpf LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':cpf' => $cpf]);
        return (bool) $stmt->fetchColumn();
    }

    public static function nomeExists(string $nome): bool {
        $pdo = self::getPDO();
        $sql = "SELECT 1 FROM Usuarios WHERE nome = :nome LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':nome' => $nome]);
        return (bool) $stmt->fetchColumn();
    }

    private static function endsWithTechfit(string $email): bool {
        return str_ends_with(strtolower($email), '@techfit.com');
    }

    public static function register(string $nome, string $email, string $cpf, string $data_nascimento, string $senha): array {
        try {
            // validações mínimas
            if (empty($nome) || empty($email) || empty($cpf) || empty($data_nascimento) || empty($senha)) {
                return ['sucesso' => false, 'erro' => 'Todos os campos são obrigatórios'];
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['sucesso' => false, 'erro' => 'Email inválido'];
            }

            if (self::emailExists($email)) {
                return ['sucesso' => false, 'erro' => 'Este email já foi registrado'];
            }

            if (self::cpfExists($cpf)) {
                return ['sucesso' => false, 'erro' => 'Este CPF já foi registrado'];
            }

            if (self::nomeExists($nome)) {
                return ['sucesso' => false, 'erro' => 'Este nome de usuário já existe'];
            }

            $tipo = self::endsWithTechfit($email) ? 'funcionario' : 'aluno';

            $pdo = self::getPDO();
            $pdo->beginTransaction();

            $sql = "INSERT INTO Usuarios (nome, email, cpf, data_nascimento, senha_hash, tipo, Avatar) VALUES (:nome, :email, :cpf, :data_nascimento, :senha_hash, :tipo, :avatar)";
            $stmt = $pdo->prepare($sql);
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $avatar = 'public/images/pfp/placeholder.png';
            $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':cpf' => $cpf,
                ':data_nascimento' => $data_nascimento,
                ':senha_hash' => $senhaHash,
                ':tipo' => $tipo,
                ':avatar' => $avatar
            ]);

            $usuarioId = (int) $pdo->lastInsertId();

            if ($tipo === 'funcionario') {
                // Não inserir na tabela Funcionarios: apenas criar em Usuarios com tipo 'funcionario'.
                $idExtra = null;
            } else {
                // Inserir registro mínimo em Alunos (ajustado ao schema atual)
                $codigoAcesso = bin2hex(random_bytes(6));

                $sqlA = "INSERT INTO Alunos (genero, endereco, telefone, codigo_acesso, id_usuario) VALUES (:genero, :endereco, :telefone, :codigo_acesso, :id_usuario)";
                $stmtA = $pdo->prepare($sqlA);
                $stmtA->execute([
                    ':genero' => 'N/D',
                    ':endereco' => '',
                    ':telefone' => '',
                    ':codigo_acesso' => $codigoAcesso,
                    ':id_usuario' => $usuarioId
                ]);
                $idExtra = (int) $pdo->lastInsertId();
            }

            $pdo->commit();

            return ['sucesso' => true, 'mensagem' => 'Cadastro realizado com sucesso', 'usuario_id' => $usuarioId, 'id_extra' => $idExtra, 'tipo' => $tipo];

        } catch (Exception $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return ['sucesso' => false, 'erro' => 'Erro ao criar cadastro: ' . $e->getMessage()];
        }
    }
}

?>
