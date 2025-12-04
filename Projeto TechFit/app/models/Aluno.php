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

}