<?php

class Aulas
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

    /**
     * Retorna o número de alunos inscritos em uma aula específica
     * @param int $id_aula ID da aula
     * @return int Número de alunos inscritos
     */
    public static function getInscritos(int $id_aula): int
    {
        $pdo = self::getPDO();
        $sql = 'SELECT COUNT(*) FROM Agendamento WHERE id_aula = :id_aula';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id_aula', $id_aula, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public static function getAulas(?string $modalidade = null): array
    {
        $pdo = self::getPDO();

        $sql = "
         SELECT
            A.*,
            M.nome_modalidade,
            F.nome_filial
        FROM
            Aulas AS A
        INNER JOIN
            Modalidades AS M ON A.id_modalidade = M.id_modalidade
        INNER JOIN
            Filiais AS F ON A.id_filial = F.id_filial ";

        if ($modalidade !== null && $modalidade !== 'todas') {
            $sql .= " WHERE A.id_modalidade = :id_modalidade";
        }

        $stmt = $pdo->prepare($sql);

        if ($modalidade !== null && $modalidade !== 'todas') {
            $stmt->bindValue(':id_modalidade', $modalidade, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function checkAgendado(int $id_aluno, int $id_aula): bool
    {
        $pdo = self::getPDO();
        $sql = "SELECT COUNT(*) FROM Aulas_Aluno WHERE id_aula = :id_aula AND id_aluno = :id_aluno";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id_aula', $id_aula, PDO::PARAM_INT);
        $stmt->bindValue(':id_aluno', $id_aluno, PDO::PARAM_INT);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Retorna as aulas de um aluno com filtro opcional por modalidade
     * @param int $id_aluno ID do aluno
     * @param string|null $id_modalidade ID da modalidade ou 'todas'
     * @return array Lista de aulas do aluno
     */
    public static function getAulasByAluno(int $id_aluno, ?string $id_modalidade = null): array
    {
        $pdo = self::getPDO();
        $sql = "
            SELECT
                A.id_aula,
                A.dia_aula,
                A.quantidade_pessoas,
                A.nome_aula,
                A.descricao,
                M.nome_modalidade,
                F.nome_filial
            FROM Aulas A
            JOIN Modalidades M ON A.id_modalidade = M.id_modalidade
            JOIN Filiais F ON A.id_filial = F.id_filial
            JOIN Agendamento Ag ON A.id_aula = Ag.id_aula
            WHERE Ag.id_aluno = :id_aluno
              AND Ag.status = 'agendado'
        ";

        if ($id_modalidade !== null && $id_modalidade !== 'todas') {
            $sql .= " AND A.id_modalidade = :id_modalidade";
        }
        $sql .= " ORDER BY A.dia_aula";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id_aluno', $id_aluno, PDO::PARAM_INT);

        if ($id_modalidade !== null && $id_modalidade !== 'todas') {
            $stmt->bindValue(':id_modalidade', $id_modalidade, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
