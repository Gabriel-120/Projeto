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

        $sql  = "SELECT id_usuario, nome, email, cpf, tipo, avatar FROM Usuarios WHERE id_usuario = :id_usuario";
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
            'user_cpf'       => $usuario['cpf'] ?? '',
            'user_tipo'      => $usuario['tipo'],
            'user_avatar'    => $usuario['avatar'] ?: '/assets/images/upload/pfp/avatar.png',
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
     * Verifica se um email já existe
     * @param string $email Email a verificar
     * @param int|null $excludeUserId Se fornecido, exclui um usuário da busca (para update)
     * @return bool True se email existe
     */
    public static function emailJaExiste(string $email, ?int $excludeUserId = null): bool
    {
        $pdo = self::getPDO();
        $sql = "SELECT COUNT(*) FROM Usuarios WHERE email = :email";
        
        if ($excludeUserId !== null) {
            $sql .= " AND id_usuario != :id";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        if ($excludeUserId !== null) {
            $stmt->bindValue(':id', $excludeUserId, PDO::PARAM_INT);
        }
        $stmt->execute();
        
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Alias para emailJaExiste (usado em configController)
     */
    public static function emailExists(string $email, ?int $excludeUserId = null): bool
    {
        return self::emailJaExiste($email, $excludeUserId);
    }

    /**
     * Verifica se um CPF já existe
     * @param string $cpf CPF a verificar (apenas dígitos)
     * @return bool True se CPF existe
     */
    public static function cpfJaExiste(string $cpf): bool
    {
        $pdo = self::getPDO();
        $sql = "SELECT COUNT(*) FROM Usuarios WHERE cpf = :cpf";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':cpf', $cpf, PDO::PARAM_STR);
        $stmt->execute();
        
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Obtém usuário por email
     * @param string $email Email do usuário
     * @return array|null Dados do usuário ou null se não encontrado
     */
    public static function getUsuarioByEmail(string $email): ?array
    {
        $pdo = self::getPDO();
        $sql = "SELECT id_usuario, nome, email, cpf, tipo, senha_hash, avatar FROM Usuarios WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        return $usuario ?: null;
    }

    /**
     * Cria um novo usuário
     * @param string $nome Nome do usuário
     * @param string $email Email do usuário
     * @param string $cpf CPF (apenas dígitos)
     * @param string $tipo 'usuario' ou 'funcionario'
     * @param string $senhaHash Senha já com hash
     * @return int ID do usuário criado
     */
    public static function criar(string $nome, string $email, string $cpf, string $tipo, string $senhaHash): int
    {
        $pdo = self::getPDO();
        $sql = "INSERT INTO Usuarios (nome, email, cpf, tipo, senha_hash, avatar) 
                VALUES (:nome, :email, :cpf, :tipo, :senha, :avatar)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':cpf', $cpf, PDO::PARAM_STR);
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindValue(':senha', $senhaHash, PDO::PARAM_STR);
        $stmt->bindValue(':avatar', '/assets/images/upload/pfp/avatar.png', PDO::PARAM_STR);
        $stmt->execute();
        
        return (int)$pdo->lastInsertId();
    }

    /**
     * Obtém o hash da senha
     * @param int $id ID do usuário
     * @return string|null Hash da senha ou null se não encontrado
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
     * @param string $newAvatar Caminho do novo avatar
     */
    public static function changeAvatar(int $id, string $newAvatar): void
    {
        $pdo = self::getPDO();
        $sql = "UPDATE Usuarios SET avatar = :avatar WHERE id_usuario = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':avatar', $newAvatar, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
