<?php require __DIR__ . '/partials/nav.php'; ?>

<div class="container py-5">
    <h1 class="text-center pb-4">Planos Disponíveis</h1>
    <div class="row">
        <?php foreach ($planos ?? [] as $p):
            $preco = number_format((float)$p['preco'], 2, '.', '');
            $planoNome = urlencode($p['nome_plano']);
        ?>
        <div class="col-lg-4 p-2">
            <div class="card shadow-sm h-100 d-flex flex-column p-4">
                <h4 class="card-title"><?php echo htmlspecialchars($p['nome_plano']); ?></h4>
                <h5 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($p['descricao_plano']); ?></h5>
                <div class="card-body text-center">
                    <h2>R$<?php echo number_format($p['preco'], 2, ',', '.'); ?></h2>
                    <span>/<?php echo intval($p['duracao']) === 30 ? 'mês' : intval($p['duracao']) . ' dias'; ?></span>
                </div>
                <ul class="py-3">
                    <li>Acesso às modalidades inclusas</li>
                </ul>
                <a href="/pagamentos.php?id_plano=<?php echo (int)$p['id_plano']; ?>" class="btn btn-primary mt-auto">Assinar</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
