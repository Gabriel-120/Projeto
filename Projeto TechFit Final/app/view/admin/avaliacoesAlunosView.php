

<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Avaliações de Alunos</h2>

    <form method="GET" action="/profile" class="mb-4 flex gap-2 items-end">
        <input type="hidden" name="page" value="avaliacoes_alunos">

        <div class="flex-1">
            <label for="id_aluno" class="block mb-1">Aluno</label>
            <select id="id_aluno" name="id_aluno" class="border rounded px-2 py-1 w-full" required>
                <option value="">Selecione um aluno...</option>
                <?php foreach ($alunos as $al): ?>
                    <option value="<?php echo (int)$al['id_aluno']; ?>"
                        <?php echo ($id_aluno_selecionado == $al['id_aluno']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($al['nome']) . ' (' . htmlspecialchars($al['codigo_acesso'] ?? '') . ')'; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            Carregar
        </button>
    </form>

    <?php if ($id_aluno_selecionado): ?>

        <h3 class="text-xl font-semibold mb-2">Histórico de Avaliações</h3>

        <?php if (empty($avaliacoes)): ?>
            <p class="mb-4">Este aluno ainda não possui avaliações cadastradas.</p>
        <?php else: ?>
            <ul class="space-y-3 mb-4">
                <?php foreach ($avaliacoes as $av): ?>
                    <li class="border border-gray-200 rounded p-3">
                        <div class="flex justify-between">
                            <span>Nota: <?php echo number_format($av['nota'], 1, ',', '.'); ?></span>
                            <span>Instrutor: <?php echo htmlspecialchars($av['nome_instrutor'] ?? ''); ?></span>
                        </div>
                        <p class="mt-2"><?php echo htmlspecialchars($av['comentarios']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <h3 class="text-xl font-semibold mb-2">Nova Avaliação para este aluno</h3>

        <form method="POST" action="/profile?page=avaliacoes_alunos" class="space-y-3">
            <input type="hidden" name="action" value="nova_avaliacao_aluno">
            <input type="hidden" name="id_aluno" value="<?php echo (int)$id_aluno_selecionado; ?>">

            <div>
                <label for="nota" class="block mb-1">Nota (0 a 10)</label>
                <input type="number" step="0.1" min="0" max="10" id="nota" name="nota"
                       class="border rounded px-2 py-1" required>
            </div>

            <div>
                <label for="comentarios" class="block mb-1">Comentário</label>
                <textarea id="comentarios" name="comentarios" rows="3"
                          class="w-full border rounded px-2 py-1" required></textarea>
            </div>

            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
                Salvar Avaliação
            </button>
        </form>
    <?php else: ?>
        <p>Selecione um aluno acima para ver e cadastrar avaliações.</p>
    <?php endif; ?>
</div>