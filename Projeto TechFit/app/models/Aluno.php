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
        $sql = "SELECT id_aluno, nome_aluno, genero, data_nascimento, endereco, telefone FROM Alunos WHERE id_usuario = :user_id";
        $sql = $pdo->prepare($sql);
        $sql->execute([":user_id" => $user_id]);
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

}