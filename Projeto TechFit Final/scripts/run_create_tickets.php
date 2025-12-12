<?php
require_once __DIR__ . '/../app/models/Connect.php';

try {
    $pdo = Connect::conectar();
    $sql = file_get_contents(__DIR__ . '/create_ticket_mensagens.sql');
    $pdo->exec($sql);
    echo '✓ Tabela TicketMensagens criada com sucesso!';
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
}
?>
