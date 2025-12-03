<?php

class Modalidades
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
     * Busca as modalidades para as quais um aluno tem aulas AGENDADAS.
     *
     * @param int $id ID do aluno
     * @return array Lista de modalidades (id e nome)
     */
    public static function getModalidadesAgendadasByAluno(int $id): array
    {
        $pdo = self::getPDO();

        // Esta query busca as modalidades ÚNICAS (DISTINCT)
        // baseando-se nos agendamentos ATIVOS do aluno.
        $sql = "SELECT DISTINCT
                    M.id_modalidade,
                    M.nome_modalidade
                FROM
                    Agendamento AS Ag
                INNER JOIN Aulas AS A ON Ag.id_aula = A.id_aula
                INNER JOIN Modalidades AS M ON A.id_modalidade = M.id_modalidade
                WHERE
                    Ag.id_aluno = :id_aluno
                    AND Ag.status = 'agendado'";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_aluno', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca TODAS as modalidades oferecidas pela academia.
     * @return array
     */
    public static function getModalidades(): array
    {
        $pdo = self::getPDO();
        $sql = "SELECT
                    id_modalidade,
                    nome_modalidade
                FROM Modalidades
                ORDER BY nome_modalidade";

        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca uma modalidade específica pelo ID.
     * @param int $id_modalidade
     * @return ?array
     */
    public static function getModalidadeById(int $id_modalidade): ?array
    {
        $pdo = self::getPDO();
        $sql = "SELECT
                    id_modalidade,
                    nome_modalidade,
                    descricao
                FROM Modalidades
                WHERE id_modalidade = :id_modalidade";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_modalidade', $id_modalidade, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}