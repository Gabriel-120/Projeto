
<div class="max-w-3xl mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">📅 Minha Agenda</h2>

    <!-- Filtro de Modalidades -->
    <div class="mb-6">
        <label for="modalidade" class="font-semibold text-gray-700 block mb-2">Filtrar por Modalidade:</label>
        <select id="modalidade"
                class="block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                onchange="updateModalidade(this)">

            <option value="todas"
            <?=$modalidadeSelecionada == 'todas' ? 'selected' : '' ?>>
                Todas as Modalidades
            </option>

            <?php foreach ($modalidadesAluno as $modalidade): ?>
                <option value="<?php echo $modalidade['id_modalidade'] ?>"<?php echo $modalidadeSelecionada == $modalidade['id_modalidade'] ? 'selected' : '' ?>>
                    <?php echo htmlspecialchars($modalidade['nome_modalidade']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Minhas Aulas Agendadas</h3>

        <?php if (empty($aulasAluno)): ?>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                <p class="text-gray-600">
                    Você não tem aulas agendadas
                    <?php if ($modalidadeSelecionada !== 'todas'): ?>
                        para esta modalidade.
                    <?php else: ?>
                        no momento.
                    <?php endif; ?>
                </p>
                <a href="/aulas" class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 transition">
                    Ver Aulas Disponíveis
                </a>
            </div>

        <?php else: ?>
            <!-- Loop das Aulas -->
            <div class="space-y-4">
                <?php foreach ($aulasAluno as $aula): ?>
                    <div class="border rounded-lg p-4 shadow-sm bg-white transition hover:shadow-md">
                    <h3 class="text-xl text-blue-900 font-bold"><?=$aula['nome_aula']?></h3>    
                        <p class="font-bold italic text-lg text-blue-700">
                            <?php echo htmlspecialchars($aula['nome_modalidade']) ?>
                        </p>
                        <div class="mt-2 text-sm text-gray-700 space-y-1">                            
                            <p><strong>Local:</strong>                                                                                                                                 <?php echo htmlspecialchars($aula['nome_filial']) ?></p>
                            <p><strong>Data:</strong>                                                     
                            <?php echo date('d/m/Y', strtotime($aula['dia_aula'])) ?>
                        </p>
                            <p><strong>Sobre a aula:</strong><?=$aula["descricao"]?></p>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100">
                        <form action="/profile?page=agenda" method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="cancelar">
                            <input type="hidden" name="agendamento_id" value="<?php echo $aula['id_aula']; ?>">
                            <button type="submit" class="btn-cancelar">Cancelar</button>
                        </form>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Script para o filtro -->
<script>
    function updateModalidade(selectElement) {
        const modalidade = selectElement.value;
        // Atualiza a URL sem recarregar a página (apenas para o filtro)
        const url = new URL(window.location);
        url.searchParams.set('modalidade', modalidade);
        // Recarrega a página com o novo parâmetro
        window.location.href = url.href;
    }
</script>