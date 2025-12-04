<?php 
class Aluno {
    private static ?PDO $pdo = null;
    private static function getPDO(): PDO{
        if(self::$pdo === null){
            self::$pdo = Connect::conectar();
        }
        return self::$pdo;
    }
    public static function getAlunoByUserID(int $user_id){
        $pdo = self::getPDO();
        // Columns according to the current DB schema (see Fisico techfit final.sql):
        // id_aluno, genero, endereco, telefone, codigo_acesso, id_usuario
        $sql = "SELECT id_aluno, genero, endereco, telefone, codigo_acesso, id_usuario FROM Alunos WHERE id_usuario = :user_id";
        $sql = $pdo->prepare($sql);
        $sql->execute([":user_id" => $user_id]);
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cria um novo aluno
     * @param int $id_usuario ID do usuário relacionado
     * @param string $genero Gênero do aluno
     * @param string $endereco Endereço
     * @param string $telefone Telefone (formatado)
     * @param string $data_nascimento Data de nascimento (YYYY-MM-DD)
     * @return int ID do aluno criado
     */
    public static function criarAluno(int $id_usuario, string $genero, string $endereco, string $telefone, string $data_nascimento): int
    {
        $pdo = self::getPDO();
        $codigoAcesso = strtoupper(substr(md5(uniqid()), 0, 6));
        
        $sql = "INSERT INTO Alunos (id_usuario, genero, endereco, telefone, data_nascimento, codigo_acesso) 
                VALUES (:id_usuario, :genero, :endereco, :telefone, :data_nascimento, :codigo_acesso)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->bindValue(':genero', $genero, PDO::PARAM_STR);
        $stmt->bindValue(':endereco', $endereco, PDO::PARAM_STR);
        $stmt->bindValue(':telefone', $telefone, PDO::PARAM_STR);
        $stmt->bindValue(':data_nascimento', $data_nascimento, PDO::PARAM_STR);
        $stmt->bindValue(':codigo_acesso', $codigoAcesso, PDO::PARAM_STR);
        $stmt->execute();
        
        return (int)$pdo->lastInsertId();
    }

    /**
     * Atualiza dados de um aluno
     * @param int $id_aluno ID do aluno
     * @param array $dados Dados a atualizar (genero, endereco, telefone, etc)
     */
    public static function updateAluno(int $id_aluno, array $dados): void
    {
        $pdo = self::getPDO();
        
        $campos = [];
        $valores = [];
        
        foreach ($dados as $chave => $valor) {
            $campos[] = "$chave = :$chave";
            $valores[":$chave"] = $valor;
        }
        
        if (empty($campos)) {
            return;
        }
        
        $sql = "UPDATE Alunos SET " . implode(", ", $campos) . " WHERE id_aluno = :id_aluno";
        $valores[':id_aluno'] = $id_aluno;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($valores);
    }
}