
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Minha Frequência</h2>

    <?php if (empty($frequencias)): ?>
        <p>Você ainda não possui registros de entrada na academia.</p>
    <?php else: ?>
        <table class="min-w-full border border-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 border-b text-left">Data / Hora</th>
                    <th class="px-4 py-2 border-b text-left">Filial</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($frequencias as $freq): ?>
                    <tr>
                        <td class="px-4 py-2 border-b">
                            <?php 
                                // formata a data/hora
                                $dt = new DateTime($freq['data_checkin']);
                                echo $dt->format('d/m/Y H:i');
                            ?>
                        </td>
                        <td class="px-4 py-2 border-b">
                            <?php echo htmlspecialchars($freq['nome_filial']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>