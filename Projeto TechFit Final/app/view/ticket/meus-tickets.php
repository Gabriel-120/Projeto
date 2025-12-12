<?php require_once __DIR__ . "/../partials/nav.php"; ?>

<div class="container my-4">
    <h1 class="mb-4">Meus Tickets de Suporte</h1>

    <!-- Abrir novo ticket -->
    <div class="row mb-4">
        <div class="col-md-12">
            <a href="/contato" class="btn btn-primary btn-lg">+ Abrir Novo Ticket</a>
        </div>
    </div>

    <!-- Tickets -->
    <?php if (empty($tickets)): ?>
        <div class="alert alert-info" role="alert">
            <h5>Nenhum ticket ainda</h5>
            <p>Você não abriu nenhum ticket de suporte. Clique no botão acima para abrir um novo.</p>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($tickets as $t): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-<?php 
                            $status = $t['status'] ?? 'aberto';
                            echo ($status === 'resolvido' || $status === 'fechado') ? 'success' : (($status === 'em_andamento') ? 'warning' : 'info');
                        ?> text-white">
                            <h5 class="mb-0"><?php echo htmlspecialchars($t['categoria_suporte']); ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <label class="fw-bold">ID do Ticket:</label>
                                <code><?php echo htmlspecialchars(substr($t['ticket'], 0, 12)); ?>...</code>
                            </div>
                            
                            <div class="mb-2">
                                <label class="fw-bold">Status:</label>
                                <span class="badge bg-<?php 
                                    $status = $t['status'] ?? 'aberto';
                                    echo ($status === 'resolvido' || $status === 'fechado') ? 'success' : (($status === 'em_andamento') ? 'warning' : 'info');
                                ?>">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </div>

                            <div class="mb-3">
                                <label class="fw-bold">Descrição:</label>
                                <p class="text-muted"><?php echo htmlspecialchars(substr($t['descricao_suporte'], 0, 100)); ?>...</p>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="/meutickets?ticket=<?php echo urlencode($t['ticket']); ?>" class="btn btn-sm btn-primary">Ver Conversa</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .card {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .card-header {
        border-bottom: none;
    }

    .badge {
        font-size: 0.85rem;
        padding: 0.5rem 0.75rem;
    }
</style>
