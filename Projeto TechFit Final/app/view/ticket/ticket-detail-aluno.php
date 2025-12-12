<?php require_once __DIR__ . "/../partials/nav.php"; ?>

<div class="container my-4">
    <h1 class="mb-4">Detalhes do Ticket</h1>

    <div class="row">
        <!-- Informações do Ticket -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informações do Ticket</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold">ID do Ticket:</label>
                        <p><code><?php echo htmlspecialchars($ticket['ticket']); ?></code></p>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Categoria:</label>
                        <p><?php echo htmlspecialchars($ticket['categoria_suporte']); ?></p>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Status:</label>
                        <p>
                            <span class="badge bg-<?php 
                                $status = $ticket['status'] ?? 'aberto';
                                echo ($status === 'resolvido' || $status === 'fechado') ? 'success' : (($status === 'em_andamento') ? 'warning' : 'info');
                            ?>">
                                <?php echo htmlspecialchars($status); ?>
                            </span>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Descrição:</label>
                        <p><?php echo nl2br(htmlspecialchars($ticket['descricao_suporte'])); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat de Mensagens -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Conversa com o Suporte</h5>
                </div>
                <div class="card-body" style="height: 500px; overflow-y: auto; background: #f8f9fa;">
                    <?php if (empty($mensagens)): ?>
                        <p class="text-muted text-center mt-5">Nenhuma resposta ainda. Aguarde o atendimento do suporte.</p>
                    <?php else: ?>
                        <?php foreach ($mensagens as $msg): ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <strong><?php echo htmlspecialchars($msg['nome']); ?></strong>
                                    <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($msg['data_envio'])); ?></small>
                                </div>
                                <div class="bg-white p-3 rounded mt-1" style="border-left: 4px solid #667eea;">
                                    <?php echo nl2br(htmlspecialchars($msg['conteudo'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Formulário para enviar mensagem -->
                <div class="card-footer bg-light">
                    <form method="post">
                        <div class="input-group">
                            <textarea name="mensagem" class="form-control" placeholder="Digite sua mensagem..." rows="3" required></textarea>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="/meutickets" class="btn btn-secondary">Voltar aos Meus Tickets</a>
    </div>
</div>

<style>
    .card-body {
        padding: 1.5rem;
    }
    textarea {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
</style>
