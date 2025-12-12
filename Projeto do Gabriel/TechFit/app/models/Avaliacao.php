<?php

class Avaliacao
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
     * Lista avaliações feitas por um aluno, com o nome do instrutor.
     */
    public static function getByAluno(int $id_aluno): array
    {
        $pdo = self::getPDO();

        $sql = "
            SELECT 
                a.id_avaliacao,
                a.comentarios,
                a.nota,
                a.id_funcionario,
                u.nome AS nome_instrutor
            FROM avaliacoes a
            LEFT JOIN funcionarios f ON a.id_funcionario = f.id_funcionario
            LEFT JOIN usuarios u ON f.id_usuario = u.id_usuario
            WHERE a.id_aluno = :id_aluno
            ORDER BY a.id_avaliacao DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id_aluno', $id_aluno, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function criar(int $id_aluno, int $id_funcionario, float $nota, string $comentarios): void
    {
        $pdo = self::getPDO();

        $sql = "
            INSERT INTO avaliacoes (comentarios, nota, id_aluno, id_funcionario)
            VALUES (:comentarios, :nota, :id_aluno, :id_funcionario)
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':comentarios', $comentarios, PDO::PARAM_STR);
        $stmt->bindValue(':nota', $nota);
        $stmt->bindValue(':id_aluno', $id_aluno, PDO::PARAM_INT);
        $stmt->bindValue(':id_funcionario', $id_funcionario, PDO::PARAM_INT);
        $stmt->execute();
    }
}