<div class="max-w-5xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Gerenciar Tickets de Suporte</h2>

    <form method="GET" action="/profile" class="mb-4">
        <input type="hidden" name="page" value="suporte_admin">
        <label for="status">Filtrar por status:</label>
        <select name="status" id="status" onchange="this.form.submit()" class="border rounded px-2 py-1">
            <option value="todos" <?php echo $statusFiltro === 'todos' ? 'selected' : ''; ?>>Todos</option>
            <option value="Aberto" <?php echo $statusFiltro === 'Aberto' ? 'selected' : ''; ?>>Aberto</option>
            <option value="Em Andamento" <?php echo $statusFiltro === 'Em Andamento' ? 'selected' : ''; ?>>Em Andamento</option>
            <option value="Resolvido" <?php echo $statusFiltro === 'Resolvido' ? 'selected' : ''; ?>>Resolvido</option>
        </select>
    </form>

    <?php if (empty($tickets)): ?>
        <p>Nenhum ticket encontrado.</p>
    <?php else: ?>
        <table class="min-w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border-b text-left">Ticket</th>
                    <th class="px-4 py-2 border-b text-left">Aluno</th>
                    <th class="px-4 py-2 border-b text-left">Categoria</th>
                    <th class="px-4 py-2 border-b text-left">Status</th>
                    <th class="px-4 py-2 border-b text-left">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($t['ticket']); ?></td>
                        <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($t['nome_aluno']); ?></td>
                        <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($t['categoria_suporte']); ?></td>
                        <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($t['status']); ?></td>
                        <td class="px-4 py-2 border-b">
                            <form method="POST" action="/profile?page=suporte_admin" class="inline-block">
                                <input type="hidden" name="action" value="atualizar_ticket">
                                <input type="hidden" name="ticket" value="<?php echo htmlspecialchars($t['ticket']); ?>">
                                <select name="status" class="border rounded px-1 py-1 text-xs">
                                    <option>Aberto</option>
                                    <option>Em Andamento</option>
                                    <option>Resolvido</option>
                                </select>
                                <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded text-xs">Atualizar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>