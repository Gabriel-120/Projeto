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
        // conta apenas agendamentos ativos
        $sql = 'SELECT COUNT(*) FROM agendamento WHERE id_aula = :id_aula AND status = "agendado"';

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id_aula', $id_aula, PDO::PARAM_INT);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public static function fecharAulasPassadas(): void
    {
        $pdo = self::getPDO();
        $sql = "
        UPDATE agendamento Ag
        JOIN Aulas A ON Ag.id_aula = A.id_aula
        SET Ag.status = 'presente'
        WHERE A.dia_aula <= NOW()
          AND Ag.status = 'agendado'
    ";

        $pdo->exec($sql);
    }
    public static function getAulas(?string $modalidade = null): array
    {
        $pdo = self::getPDO();

        $sql = "
         SELECT A.*,
            M.nome_modalidade,
            F.nome_filial,
            U.nome AS nome_funcionario
        FROM
            Aulas AS A
        INNER JOIN
            Modalidades AS M ON A.id_modalidade = M.id_modalidade
        INNER JOIN
            Filiais AS F ON A.id_filial = F.id_filial
		INNER JOIN
			Funcionarios AS FN ON FN.id_funcionario=A.id_funcionario
        INNER JOIN
            Usuarios AS U ON FN.id_usuario=U.id_usuario
        WHERE
            A.dia_aula > NOW()
        "; // somente aulas futuras

        if ($modalidade !== null && $modalidade !== 'todas') {
            $sql .= " AND A.id_modalidade = :id_modalidade";
        }

        $sql .= " ORDER BY A.dia_aula";

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
        $sql = "SELECT COUNT(*) FROM agendamento WHERE id_aula = :id_aula AND id_aluno = :id_aluno AND status = 'agendado'";

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
    /**
     * Agenda uma aula para o usuário logado
     * @param int $id_aula
     * @param int $id_usuario
     * @return bool true se agendou, false se já estava agendado ou lotado
     */
    public static function agendarAula(int $id_aula, int $id_usuario): bool
    {
        $pdo = self::getPDO();

        // Descobre o id_aluno a partir do id_usuario
        $aluno = Aluno::getAlunoByUserID($id_usuario);
        if (! $aluno) {
            return false;
        }
        $id_aluno = (int) $aluno['id_aluno'];

        // Verifica se já existe agendamento ativo
        if (self::checkAgendado($id_aluno, $id_aula)) {
            return false;
        }

        // Verifica capacidade da aula
        $stmt = $pdo->prepare('SELECT quantidade_pessoas FROM Aulas WHERE id_aula = :id_aula');
        $stmt->bindValue(':id_aula', $id_aula, PDO::PARAM_INT);
        $stmt->execute();
        $capacidade = (int) $stmt->fetchColumn();

        if ($capacidade > 0) {
            $inscritos = self::getInscritos($id_aula);
            if ($inscritos >= $capacidade) {
                return false;
            }
        }

        try {
            $pdo->beginTransaction();

            // Garante vínculo na tabela de relacionamento (evita erro se já existir)
            $sql = 'INSERT INTO aulas_aluno (id_aula, id_aluno) VALUES (:id_aula, :id_aluno)
                    ON DUPLICATE KEY UPDATE id_aula = VALUES(id_aula)';
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id_aula', $id_aula, PDO::PARAM_INT);
            $stmt->bindValue(':id_aluno', $id_aluno, PDO::PARAM_INT);
            $stmt->execute();

            // Cria registro de agendamento
            $sql = "INSERT INTO agendamento (data_agendamento, status, id_aula, id_aluno)
                    VALUES (NOW(), 'agendado', :id_aula, :id_aluno)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id_aula', $id_aula, PDO::PARAM_INT);
            $stmt->bindValue(':id_aluno', $id_aluno, PDO::PARAM_INT);
            $stmt->execute();

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
