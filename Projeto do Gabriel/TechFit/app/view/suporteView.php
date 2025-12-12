<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Meus Tickets de Suporte</h2>

    <?php if (empty($tickets)): ?>
        <p>Você ainda não abriu nenhum ticket de suporte.</p>
    <?php else: ?>
        <ul class="space-y-3 mb-6">
            <?php foreach ($tickets as $t): ?>
                <li class="border border-gray-200 rounded p-3">
                    <div class="flex justify-between">
                        <strong><?php echo htmlspecialchars($t['ticket']); ?></strong>
                        <span class="px-2 py-1 rounded text-sm
                            <?php echo $t['status'] === 'Aberto' ? 'bg-yellow-200' : 'bg-green-200'; ?>">
                            <?php echo htmlspecialchars($t['status']); ?>
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                        <strong>Categoria:</strong> <?php echo htmlspecialchars($t['categoria_suporte']); ?>
                    </p>
                    <p class="mt-2"><?php echo htmlspecialchars($t['descricao_suporte']); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h3 class="text-xl font-semibold mb-2">Abrir novo ticket</h3>

    <form method="POST" action="/profile?page=suporte" class="space-y-3">
        <input type="hidden" name="action" value="novo_ticket">

        <div>
            <label for="categoria" class="block mb-1">Categoria</label>
            <select id="categoria" name="categoria" class="border rounded px-2 py-1 w-full" required>
                <option value="">Selecione...</option>
                <option value="Equipamento">Equipamento</option>
                <option value="Agendamento">Agendamento</option>
                <option value="Pagamento">Pagamento</option>
                <option value="Outro">Outro</option>
            </select>
        </div>

        <div>
            <label for="descricao" class="block mb-1">Descrição do problema</label>
            <textarea id="descricao" name="descricao" rows="4"
                      class="w-full border rounded px-2 py-1" required></textarea>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            Enviar Ticket
        </button>
    </form>
</div>