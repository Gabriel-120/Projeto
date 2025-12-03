<?php require_once __DIR__ . "/../view/partials/nav.php"; ?>

<div class="flex flex-col items-center justify-center px-6 py-10 mt-4">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Aulas Disponíveis</h1>
    <form method="get" id="filtroAulas" class="w-full max-w-md mb-8">
        <label for="modalidade" class="block mb-2 text-sm font-medium text-gray-700">Filtrar por modalidade</label>
        <select
            name="modalidade"
            id="modalidade"
            onchange="document.getElementById('filtroAulas').submit()"
            class="block w-full rounded-lg border-gray-300 bg-white text-gray-700 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300 focus:ring-opacity-40"
        >
            <option value="todas"                                                                                                    <?php echo $modalidadeSelecionada == 'todas' ? 'selected' : '' ?>>Todas</option>
            <?php foreach ($modalidades as $modalidade): ?>
                <option value="<?php echo $modalidade['id_modalidade'] ?>"<?php echo $modalidadeSelecionada == $modalidade['id_modalidade'] ? 'selected' : '' ?>>
                    <?php echo htmlspecialchars($modalidade["nome_modalidade"]) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>


    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 w-full max-w-6xl justify-items-center content-center">
        <?php if (empty($aulas)): ?>
            <div class="col-span-full text-center bg-gray-50 border border-gray-200 rounded-xl p-10">
            <img
            src="assets/images/clock.svg"
            alt="Relógio"
            class="mx-auto mb-4 w-16 h-16 opacity-70"
            >
                <h2 class="text-lg font-semibold text-gray-700 mb-2">😕 Não há aulas disponíveis no momento</h2>
                <p class="text-gray-500">Tente novamente mais tarde ou selecione outra modalidade.</p>
            </div>
        <?php else: ?>
            <?php foreach ($aulas as $aula): ?>
                <div class="flex flex-col justify-between items-center text-center border border-gray-200 bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition duration-200 h-full w-full min-h-[340px]">
                    <div>
                        <h2 class="text-lg font-semibold text-blue-700 text-center mb-2"><?php echo htmlspecialchars($aula["nome_aula"]) ?></h2>
                        <p class="text-m font-semibold text-gray-900 text-center mb-2"><?php echo htmlspecialchars($aula["nome_modalidade"]) ?></p>
                        <p class="text-gray-600 text-sm mb-3 text-center"><?php echo htmlspecialchars($aula["descricao"]) ?></p>
                        <p class="text-sm text-gray-700"><strong>Filial:</strong>                                                                                  <?php echo htmlspecialchars($aula["nome_filial"]) ?></p>
                        <p class="text-sm text-gray-700"><strong>Vagas:</strong>                                                                                                                                                                                                                                                 <?php echo Aulas::getInscritos($aula["id_aula"]) ?>/<?php echo $aula["quantidade_pessoas"] ?></p>
                        <p class="text-sm text-gray-700"><strong>Data:</strong>                                                                                                                                                                                                                                              <?php echo date('d/m/Y', strtotime($aula["dia_aula"])) ?></p>
                    </div>

                    <form action="aulas/agendar" method="post" class="mt-4">
                        <input type="hidden" name="id_aula" value="<?php echo $aula['id_aula'] ?>">
                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg shadow-sm transition duration-200">
                            Agendar Aula
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
