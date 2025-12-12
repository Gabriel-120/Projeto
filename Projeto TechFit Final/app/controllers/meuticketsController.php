<?php

require_once __DIR__ . '/../models/Ticket.php';

function meuticketsController(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    
    $user_id = $_SESSION['user_id'] ?? 0;
    if ($user_id <= 0) {
        header('Location: /login');
        exit;
    }

    // Obter ID do aluno do usuário
    try {
        $pdo = Connect::conectar();
        $stmt = $pdo->prepare('SELECT id_aluno FROM Alunos WHERE id_usuario = ?');
        $stmt->execute([$user_id]);
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$aluno) {
            http_response_code(403);
            render('errors/403', 'Acesso Negado');
            return;
        }
        
        $id_aluno = $aluno['id_aluno'];
    } catch (Exception $e) {
        http_response_code(500);
        render('errors/500', 'Erro no Servidor');
        return;
    }

    // Obter ticket específico se vier na URL
    $ticket_id = $_GET['ticket'] ?? '';

    if (!empty($ticket_id)) {
        // Ver detalhes de um ticket específico
        $ticket = Ticket::getByTicket($ticket_id);

        if (!$ticket || $ticket['id_aluno'] != $id_aluno) {
            http_response_code(403);
            render('errors/403', 'Acesso Negado');
            return;
        }

        // Obter mensagens do ticket
        $mensagens = Ticket::getMensagens($ticket_id);

        // Processar novo envio de mensagem
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $conteudo = trim($_POST['mensagem'] ?? '');

            if (!empty($conteudo)) {
                Ticket::adicionarMensagem($ticket_id, $user_id, $conteudo);
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit;
            }
        }

        $data = [
            'ticket' => $ticket,
            'mensagens' => $mensagens,
            'is_aluno' => true,
        ];

        render('ticket/ticket-detail-aluno', 'Detalhes do Ticket', $data);
    } else {
        // Listar todos os tickets do aluno
        $meus_tickets = Ticket::getByAluno($id_aluno);

        $data = [
            'tickets' => $meus_tickets,
        ];

        render('ticket/meus-tickets', 'Meus Tickets', $data);
    }
}
