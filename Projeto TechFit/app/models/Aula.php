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
        // contar somente agendados (vagas ocupadas)
        $sql = "SELECT COUNT(*) FROM Agendamento WHERE id_aula = :id_aula AND status = 'agendado'";

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
                F.nome_filial,
                Ag.status AS ag_status,
                Ag.data_agendamento AS ag_data,
                Ag.id_agendamento AS ag_id
            FROM Aulas A
            JOIN Modalidades M ON A.id_modalidade = M.id_modalidade
            JOIN Filiais F ON A.id_filial = F.id_filial
                        JOIN Agendamento Ag ON A.id_aula = Ag.id_aula
                        WHERE Ag.id_aluno = :id_aluno
                            AND (Ag.status = 'agendado' OR Ag.status = 'espera')
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

    /**
     * Retorna a posição do aluno na lista de espera para uma aula (1 = primeiro da fila)
     * @param int $id_aula
     * @param int $id_aluno
     * @return int|null retorna null se não estiver na lista
     */
    public static function getWaitlistPosition(int $id_aula, int $id_aluno): ?int
    {
        $pdo = self::getPDO();
        // verificar se está na lista
        $check = $pdo->prepare("SELECT id_agendamento, data_agendamento FROM Agendamento WHERE id_aula = :id_aula AND id_aluno = :id_aluno AND status = 'espera' LIMIT 1");
        $check->execute([':id_aula' => $id_aula, ':id_aluno' => $id_aluno]);
        $row = $check->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        $myTime = $row['data_agendamento'];

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Agendamento WHERE id_aula = :id_aula AND status = 'espera' AND data_agendamento <= :mytime");
        $stmt->execute([':id_aula' => $id_aula, ':mytime' => $myTime]);
        $pos = (int)$stmt->fetchColumn();
        return $pos > 0 ? $pos : 1;
    }
}
