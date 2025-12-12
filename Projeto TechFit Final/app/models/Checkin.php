<?php

class Checkin
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
     * Retorna todos os check-ins de um aluno, com o nome da filial.
     */
    public static function getByAluno(int $id_aluno): array
    {
        $pdo = self::getPDO();

        $sql = "
            SELECT 
                c.data_checkin,
                f.nome_filial
            FROM checkin c
            JOIN filiais f ON c.id_filial = f.id_filial
            WHERE c.id_aluno = :id_aluno
            ORDER BY c.data_checkin DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id_aluno', $id_aluno, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}