<?php
class Aluno
{
    private static ?PDO $pdo = null;

    private static function getPDO(): PDO
    {
        if (self::$pdo === null) {
            self::$pdo = Connect::conectar();
        }
        return self::$pdo;
    }

    /**
     * Obtém os dados completos de um aluno pelo ID do usuário
     * @param int $user_id ID do usuário
     * @return array|null Dados do aluno ou null
     */
    public static function getAlunoByUserID(int $user_id): ?array
    {
        $pdo  = self::getPDO();
        $sql  = "SELECT id_aluno, genero, endereco, telefone, codigo_acesso FROM Alunos WHERE id_usuario = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtém os dados completos de um aluno incluindo informações do usuário
     * @param int $user_id ID do usuário
     * @return array|null Dados completos do aluno ou null
     */
    public static function getAlunoCompletoByUserID(int $user_id): ?array
    {
        $pdo = self::getPDO();
        $sql = "SELECT
                    a.id_aluno,
                    u.nome,
                    u.email,
                    u.cpf,
                    u.data_nascimento,
                    a.genero,
                    a.endereco,
                    a.telefone,
                    a.codigo_acesso,
                    u.avatar
                FROM Alunos a
                INNER JOIN Usuarios u ON a.id_usuario = u.id_usuario
                WHERE a.id_usuario = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Atualiza os dados do aluno
     * @param int $id_aluno ID do aluno
     * @param array $data Array com os campos a atualizar (genero, endereco, telefone)
     * @return bool True se atualização foi bem-sucedida
     * @throws PDOException Se ocorrer erro na atualização
     */
    public static function updateAluno(int $id_aluno, array $data): bool
    {
        $pdo = self::getPDO();

        $updates = [];
        $params  = [':id_aluno' => $id_aluno];

        if (isset($data['genero'])) {
            $updates[]         = "genero = :genero";
            $params[':genero'] = $data['genero'];
        }

        if (isset($data['endereco'])) {
            $updates[]           = "endereco = :endereco";
            $params[':endereco'] = $data['endereco'];
        }

        if (isset($data['telefone'])) {
            $updates[]           = "telefone = :telefone";
            $params[':telefone'] = $data['telefone'];
        }

        if (empty($updates)) {
            return true;
        }

        $sql  = "UPDATE Alunos SET " . implode(", ", $updates) . " WHERE id_aluno = :id_aluno";
        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        return $stmt->execute();
    }

    /**
     * Cria um novo aluno
     * @param array $dados Array com os dados do aluno (id_usuario, genero, endereco, telefone)
     * @return int|null ID do aluno criado ou null se falhar
     * @throws PDOException Se ocorrer erro na inserção
     */
    public static function criarAluno(array $dados): ?int
    {
        $pdo = self::getPDO();

        $sql = "INSERT INTO Alunos (id_usuario, genero, endereco, telefone)
                VALUES (:id_usuario, :genero, :endereco, :telefone)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id_usuario', $dados['id_usuario'], PDO::PARAM_INT);
        $stmt->bindValue(':genero', $dados['genero'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':endereco', $dados['endereco'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':telefone', $dados['telefone'] ?? '', PDO::PARAM_STR);

        $stmt->execute();
        return (int) $pdo->lastInsertId();
    }

    public static function getTodosComUsuario(): array
    {
        $pdo = self::getPDO();

        $sql = "
        SELECT
            a.id_aluno,
            a.codigo_acesso,
            u.nome
        FROM Alunos a
        JOIN Usuarios u ON a.id_usuario = u.id_usuario
        ORDER BY u.nome
    ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
