<?php

class Suporte
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
     * Gera um ID único de ticket
     */
    public static function gerarTicketID(): string
    {
        return 'TKT-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(4)));
    }

    /**
     * Cria um novo ticket de suporte
     */
    public static function criar(int $id_aluno, string $categoria, string $descricao): string
    {
        $pdo = self::getPDO();

        $ticket = self::gerarTicketID();

        $sql = "
            INSERT INTO suporte (ticket, status, categoria_suporte, descricao_suporte, id_aluno)
            VALUES (:ticket, 'Aberto', :categoria, :descricao, :id_aluno)
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':ticket', $ticket, PDO::PARAM_STR);
        $stmt->bindValue(':categoria', $categoria, PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindValue(':id_aluno', $id_aluno, PDO::PARAM_INT);
        $stmt->execute();

        return $ticket;
    }

    /**
     * Lista todos os tickets de um aluno
     */
    public static function getByAluno(int $id_aluno): array
    {
        $pdo = self::getPDO();

        $sql = "
            SELECT ticket, status, categoria_suporte, descricao_suporte
            FROM suporte
            WHERE id_aluno = :id_aluno
            ORDER BY ticket DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id_aluno', $id_aluno, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lista todos os tickets
     */
    public static function getTodos(?string $status = null): array
    {
        $pdo = self::getPDO();

        $sql = "
            SELECT 
                s.ticket,
                s.status,
                s.categoria_suporte,
                s.descricao_suporte,
                u.nome AS nome_aluno
            FROM suporte s
            JOIN alunos a ON s.id_aluno = a.id_aluno
            JOIN usuarios u ON a.id_usuario = u.id_usuario
        ";

        if ($status && $status !== 'todos') {
            $sql .= " WHERE s.status = :status";
        }

        $sql .= " ORDER BY s.ticket DESC";

        $stmt = $pdo->prepare($sql);

        if ($status && $status !== 'todos') {
            $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Atualiza o status de um ticket
     */
    public static function atualizarStatus(string $ticket, string $novoStatus): bool
    {
        $pdo = self::getPDO();

        $sql = "UPDATE suporte SET status = :status WHERE ticket = :ticket";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':status', $novoStatus, PDO::PARAM_STR);
        $stmt->bindValue(':ticket', $ticket, PDO::PARAM_STR);

        return $stmt->execute();
    }
}