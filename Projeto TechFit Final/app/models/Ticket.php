<?php

class Ticket {
    public static function getAll() {
        try {
            $pdo = Connect::conectar();
            $stmt = $pdo->query('SELECT s.ticket, s.status, s.categoria_suporte, s.descricao_suporte, u.nome, u.id_usuario, a.id_aluno FROM Suporte s JOIN Alunos a ON s.id_aluno = a.id_aluno JOIN Usuarios u ON a.id_usuario = u.id_usuario ORDER BY s.ticket DESC');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public static function getByTicket($ticket) {
        try {
            $pdo = Connect::conectar();
            $stmt = $pdo->prepare('SELECT s.*, u.nome, u.email, a.id_aluno FROM Suporte s JOIN Alunos a ON s.id_aluno = a.id_aluno JOIN Usuarios u ON a.id_usuario = u.id_usuario WHERE s.ticket = ?');
            $stmt->execute([$ticket]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return null;
        }
    }

    public static function getMensagens($ticket) {
        try {
            $pdo = Connect::conectar();
            $stmt = $pdo->prepare('SELECT tm.*, u.nome FROM TicketMensagens tm JOIN Usuarios u ON tm.id_usuario = u.id_usuario WHERE tm.ticket = ? ORDER BY tm.data_envio ASC');
            $stmt->execute([$ticket]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public static function adicionarMensagem($ticket, $id_usuario, $mensagem) {
        try {
            $pdo = Connect::conectar();
            $stmt = $pdo->prepare('INSERT INTO TicketMensagens (ticket, id_usuario, conteudo, data_envio) VALUES (?, ?, ?, NOW())');
            $stmt->execute([$ticket, $id_usuario, $mensagem]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function atualizarStatus($ticket, $status) {
        try {
            $pdo = Connect::conectar();
            $stmt = $pdo->prepare('UPDATE Suporte SET status = ? WHERE ticket = ?');
            $stmt->execute([$status, $ticket]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getByAluno($id_aluno) {
        try {
            $pdo = Connect::conectar();
            $stmt = $pdo->prepare('SELECT * FROM Suporte WHERE id_aluno = ? ORDER BY ticket DESC');
            $stmt->execute([$id_aluno]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}
