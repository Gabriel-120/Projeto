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

            $tipo = self::endsWithTechfit($email) ? 'funcionario' : 'usuario';

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
                // Inserir registro mínimo em Funcionarios
                $sqlF = "INSERT INTO Funcionarios (nome_funcionario, salario, carga_horaria, cpf_funcionario, cargo, id_usuario) VALUES (:nome_funcionario, :salario, :carga, :cpf_funcionario, :cargo, :id_usuario)";
                $stmtF = $pdo->prepare($sqlF);
                $stmtF->execute([
                    ':nome_funcionario' => $nome,
                    ':salario' => 0.00,
                    ':carga' => 0,
                    ':cpf_funcionario' => $cpf,
                    ':cargo' => 'Funcionário',
                    ':id_usuario' => $usuarioId
                ]);
                $idExtra = (int) $pdo->lastInsertId();
            } else {
                // Inserir registro mínimo em Alunos
                $codigoAcesso = bin2hex(random_bytes(6));
                $hoje = date('Y-m-d');
                $dataFim = date('Y-m-d', strtotime('+1 year'));

                $sqlA = "INSERT INTO Alunos (data_agendamento, data_nascimento, endereco, nome_aluno, telefone, genero, codigo_acesso, id_usuario, status_aluno, data_inicio, data_fim) VALUES (:data_agendamento, :data_nascimento, :endereco, :nome_aluno, :telefone, :genero, :codigo_acesso, :id_usuario, :status_aluno, :data_inicio, :data_fim)";
                $stmtA = $pdo->prepare($sqlA);
                $stmtA->execute([
                    ':data_agendamento' => $hoje,
                    ':data_nascimento' => $data_nascimento,
                    ':endereco' => '',
                    ':nome_aluno' => $nome,
                    ':telefone' => '',
                    ':genero' => 'N/D',
                    ':codigo_acesso' => $codigoAcesso,
                    ':id_usuario' => $usuarioId,
                    ':status_aluno' => 'ativo',
                    ':data_inicio' => $hoje,
                    ':data_fim' => $dataFim
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
