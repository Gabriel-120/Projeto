<?php require_once __DIR__ . "/../partials/nav.php"; ?>

<div class="container my-4">
    <h1 class="mb-4">Detalhes do Ticket</h1>

    <?php if (has_flash('success')): ?>
        <div class="alert alert-success">
            <?php foreach (get_flash('success') as $msg): ?>
                <?php echo htmlspecialchars($msg); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

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
                        <p><?php echo htmlspecialchars($ticket['ticket']); ?></p>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Aluno:</label>
                        <p><?php echo htmlspecialchars($ticket['nome']); ?></p>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Email:</label>
                        <p><?php echo htmlspecialchars($ticket['email']); ?></p>
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

                    <!-- Formulário para atualizar status -->
                    <form method="post" class="mt-3">
                        <input type="hidden" name="acao" value="atualizar_status">
                        <div class="mb-2">
                            <label class="form-label fw-bold">Atualizar Status:</label>
                            <select name="novo_status" class="form-select form-select-sm">
                                <option value="aberto" <?php echo ($ticket['status'] === 'aberto') ? 'selected' : ''; ?>>Aberto</option>
                                <option value="em_andamento" <?php echo ($ticket['status'] === 'em_andamento') ? 'selected' : ''; ?>>Em Andamento</option>
                                <option value="resolvido" <?php echo ($ticket['status'] === 'resolvido') ? 'selected' : ''; ?>>Resolvido</option>
                                <option value="fechado" <?php echo ($ticket['status'] === 'fechado') ? 'selected' : ''; ?>>Fechado</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary w-100">Atualizar</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Chat de Mensagens -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Conversa com o Aluno</h5>
                </div>
                <div class="card-body" style="height: 500px; overflow-y: auto; background: #f8f9fa;">
                    <?php if (empty($mensagens)): ?>
                        <p class="text-muted text-center mt-5">Nenhuma mensagem ainda. Inicie a conversa!</p>
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
                        <input type="hidden" name="acao" value="enviar_mensagem">
                        <div class="input-group">
                            <textarea name="mensagem" class="form-control" placeholder="Digite sua resposta..." rows="3" required></textarea>
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="/admin?active_tab=tickets" class="btn btn-secondary">Voltar aos Tickets</a>
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
