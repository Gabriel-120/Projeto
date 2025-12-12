<div class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Minhas Avaliações Físicas</h2>

    <?php if (empty($avaliacoes)): ?>
        <p>Você ainda não possui avaliações físicas registradas.</p>
    <?php else: ?>
        <ul class="space-y-3 mb-6">
            <?php foreach ($avaliacoes as $av): ?>
                <li class="border border-gray-200 rounded p-3">
                    <div class="flex justify-between">
                        <strong><?php echo htmlspecialchars($av['nome_instrutor'] ?? 'Instrutor'); ?></strong>
                        <span>Nota: <?php echo number_format($av['nota'], 1, ',', '.'); ?></span>
                    </div>
                    <p class="mt-2"><?php echo htmlspecialchars($av['comentarios']); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
