<div class="max-w-5xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6">Relatórios Gerais</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="p-4 border rounded">
            <h3 class="font-semibold mb-1">Total de alunos</h3>
            <p class="text-2xl font-bold"><?php echo (int)$totalAlunos; ?></p>
        </div>
        <div class="p-4 border rounded">
            <h3 class="font-semibold mb-1">Alunos com plano ativo</h3>
            <p class="text-2xl font-bold"><?php echo (int)$alunosAtivos; ?></p>
        </div>
        <div class="p-4 border rounded">
            <h3 class="font-semibold mb-1">Receita total aprovada</h3>
            <p class="text-2xl font-bold">
                R$ <?php echo number_format($receitaTotal, 2, ',', '.'); ?>
            </p>
        </div>
    </div>

    <h3 class="text-xl font-semibold mb-3">Frequência por filial</h3>
    <?php if (empty($frequenciaPorFilial)): ?>
        <p>Não há registros de check-in ainda.</p>
    <?php else: ?>
        <table class="min-w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border-b text-left">Filial</th>
                    <th class="px-4 py-2 border-b text-left">Total de Check-ins</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($frequenciaPorFilial as $row): ?>
                    <tr>
                        <td class="px-4 py-2 border-b">
                            <?php echo htmlspecialchars($row['nome_filial']); ?>
                        </td>
                        <td class="px-4 py-2 border-b">
                            <?php echo (int)$row['total_checkins']; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>