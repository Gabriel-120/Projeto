<?php

require_once __DIR__ . '/../models/Ticket.php';

function ticketController(): void
{
    requireFuncionario();

    session_start();
    $admin_id = $_SESSION['user_id'] ?? 0;

    // Obter ticket do GET
    $ticket_id = $_GET['ticket'] ?? '';

    // Obter dados do ticket
    $ticket = Ticket::getByTicket($ticket_id);

    if (!$ticket) {
        http_response_code(404);
        render('errors/404', 'Ticket não encontrado');
        return;
    }

    // Obter mensagens do ticket
    $mensagens = Ticket::getMensagens($ticket_id);

    // Processar novo envio de mensagem
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $acao = $_POST['acao'] ?? '';

        if ($acao === 'enviar_mensagem') {
            $conteudo = trim($_POST['mensagem'] ?? '');

            if (!empty($conteudo)) {
                Ticket::adicionarMensagem($ticket_id, $admin_id, $conteudo);
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit;
            }
        } elseif ($acao === 'atualizar_status') {
            $novo_status = trim($_POST['novo_status'] ?? '');

            if (in_array($novo_status, ['aberto', 'em_andamento', 'resolvido', 'fechado'])) {
                Ticket::atualizarStatus($ticket_id, $novo_status);
                flash('success', 'Status atualizado com sucesso!');
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
    }

    $data = [
        'ticket' => $ticket,
        'mensagens' => $mensagens,
    ];

    render('admin/ticket-detail', 'Detalhes do Ticket', $data);
}
