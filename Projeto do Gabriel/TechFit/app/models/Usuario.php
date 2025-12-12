<?php

class Usuario
{
    private static ?PDO $pdo = null;

    /**
     * Inicializa a conexão com o banco de dados
     */
    private static function getPDO(): PDO
    {
        if (self::$pdo === null) {
            self::$pdo = Connect::conectar();
        }
        return self::$pdo;
    }

    public static function getUsuarioCompleto(int $id_usuario): ?array
    {
        $pdo = self::getPDO();

        $sql  = "SELECT id_usuario, nome, email, tipo, avatar FROM Usuarios WHERE id_usuario = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (! $usuario) {
            return null;
        }

        return [
            'user_id'        => $usuario['id_usuario'],
            'user_name'      => $usuario['nome'],
            'user_email'     => $usuario['email'],
            'user_tipo'      => $usuario['tipo'],
            'user_avatar'    => $usuario['avatar'] ?: '/images/upload/pfp/avatar.png',
            'id_funcionario' => $usuario['tipo'] === 'funcionario' ? $usuario['id_usuario'] : null,
        ];
    }

    /**
     * Altera a senha do usuário
     * @param int $id ID do usuário
     * @param string $newPass Nova senha (já deve estar com hash)
     * @throws PDOException Se ocorrer erro na atualização
     */
    public static function changePass(int $id, string $newPass): void
    {
        $pdo = self::getPDO();
        $sql = "UPDATE Usuarios SET senha_hash = :senha WHERE id_usuario = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':senha', $newPass, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Altera o email do usuário
     * @param int $id ID do usuário
     * @param string $newEmail Novo email
     * @throws PDOException Se ocorrer erro na atualização
     */
    public static function changeEmail(int $id, string $newEmail): void
    {
        $pdo = self::getPDO();
        $sql = "UPDATE Usuarios SET email = :email WHERE id_usuario = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $newEmail, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Obtém o email do usuário
     * @param int $id ID do usuário
     * @return string|null Email do usuário ou null se não encontrado
     */
    public static function getEmail(int $id): ?string
    {
        $pdo = self::getPDO();
        $sql = "SELECT email FROM Usuarios WHERE id_usuario = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_COLUMN);
        return $result ?: null;
    }

    /**
     * Obtém todos os dados do usuário
     * @param int $id ID do usuário
     * @return array|null Dados do usuário ou null se não encontrado
     */
    public static function getUsuarioById(int $id): ?array
    {
        $pdo = self::getPDO();
        $sql = "SELECT
                    id_usuario,
                    nome,
                    email,
                    data_nascimento,
                    tipo_usuario
                FROM Usuarios
                WHERE id_usuario = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Obtém a senha hash do usuário
     * @param int $id ID do usuário
     * @return string|null Senha hash ou null se não encontrado
     */
    public static function getSenhaHash(int $id): ?string
    {
        $pdo = self::getPDO();
        $sql = "SELECT senha_hash FROM Usuarios WHERE id_usuario = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_COLUMN);
        return $result ?: null;
    }

    /**
     * Altera o nome do usuário
     * @param int $id ID do usuário
     * @param string $newName Novo nome
     * @throws PDOException Se ocorrer erro na atualização
     */
    public static function changeName(int $id, string $newName): void
    {
        $pdo = self::getPDO();
        $sql = "UPDATE Usuarios SET nome = :nome WHERE id_usuario = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nome', $newName, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Altera o avatar do usuário
     * @param int $id ID do usuário
     * @param string $avatarPath Caminho do novo avatar
     * @throws PDOException Se ocorrer erro na atualização
     */
    public static function changeAvatar(int $id, string $avatarPath): void
    {
        $pdo = self::getPDO();
        $sql = "UPDATE Usuarios SET avatar = :avatar WHERE id_usuario = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':avatar', $avatarPath, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * Verifica se um email já existe no banco (excluindo o usuário atual)
     * @param string $email Email a verificar
     * @param int $excludeId ID do usuário a excluir da verificação
     * @return bool True se o email já existe, false caso contrário
     */
    public static function emailExists(string $email, int $excludeId): bool
    {
        $pdo = self::getPDO();
        $sql = "SELECT COUNT(*) FROM Usuarios WHERE email = :email AND id_usuario != :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':id', $excludeId, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Obtém um usuário pelo email
     * @param string $email Email do usuário
     * @return array|null Dados do usuário ou null se não encontrado
     */
    public static function getUsuarioByEmail(string $email): ?array
    {
        $pdo = self::getPDO();
        $sql = "SELECT id_usuario, nome, email, tipo FROM Usuarios WHERE email = :email";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Verifica se um email já existe no banco
     * @param string $email Email a verificar
     * @return bool True se o email já existe, false caso contrário
     */
    public static function emailJaExiste(string $email): bool
    {
        $pdo = self::getPDO();
        $sql = "SELECT COUNT(*) FROM Usuarios WHERE email = :email";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Verifica se um CPF já existe no banco
     * @param string $cpf CPF a verificar
     * @return bool True se o CPF já existe, false caso contrário
     */
    public static function cpfJaExiste(string $cpf): bool
    {
        $pdo = self::getPDO();
        $sql = "SELECT COUNT(*) FROM Usuarios WHERE cpf = :cpf";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':cpf', $cpf, PDO::PARAM_STR);
        $stmt->execute();

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Cria um novo usuário
     * @param array $dados Array com os dados do usuário (nome, email, cpf, data_nascimento, senha_hash, tipo)
     * @return int|null ID do usuário criado ou null se falhar
     * @throws PDOException Se ocorrer erro na inserção
     */
    public static function criar(array $dados): ?int
    {
        $pdo = self::getPDO();
        
        $sql = "INSERT INTO Usuarios (nome, email, cpf, data_nascimento, senha_hash, tipo, avatar) 
                VALUES (:nome, :email, :cpf, :data_nascimento, :senha_hash, :tipo, :avatar)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nome', $dados['nome'], PDO::PARAM_STR);
        $stmt->bindValue(':email', $dados['email'], PDO::PARAM_STR);
        $stmt->bindValue(':cpf', $dados['cpf'], PDO::PARAM_STR);
        $stmt->bindValue(':data_nascimento', $dados['data_nascimento'], PDO::PARAM_STR);
        $stmt->bindValue(':senha_hash', $dados['senha_hash'], PDO::PARAM_STR);
        $stmt->bindValue(':tipo', $dados['tipo'], PDO::PARAM_STR);
        $stmt->bindValue(':avatar', '/images/upload/pfp/avatar.png', PDO::PARAM_STR);

        $stmt->execute();
        return (int) $pdo->lastInsertId();
    }
}
