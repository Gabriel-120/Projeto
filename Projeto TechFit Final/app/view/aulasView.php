<?php require_once __DIR__ . "/../view/partials/nav.php"; ?>

<div class="flex flex-col items-center justify-center px-6 py-12 mt-4">

    <!-- TÍTULO DA PÁGINA -->
    <h1 class="text-3xl font-bold text-gray-900 mb-10">Aulas Disponíveis</h1>

    <!-- FILTRO -->
    <form method="get" id="filtroAulas" class="w-full max-w-md mb-10">
        <label for="modalidade" class="block mb-2 text-sm font-semibold text-gray-700">
            Filtrar por modalidade
        </label>

        <select
            name="modalidade"
            id="modalidade"
            onchange="document.getElementById('filtroAulas').submit()"
            class="block w-full rounded-xl border-gray-300 bg-white text-gray-700 shadow-sm
                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
        >
            <option value="todas" <?= $modalidadeSelecionada == 'todas' ? 'selected' : '' ?>>Todas</option>

            <?php foreach ($modalidades as $modalidade): ?>
                <option value="<?= $modalidade['id_modalidade'] ?>"
                    <?= $modalidadeSelecionada == $modalidade['id_modalidade'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($modalidade["nome_modalidade"]) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <!-- GRID DE AULAS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 w-full max-w-6xl">

        <!-- NÃO EXISTE NENHUMA AULA -->
        <?php if (empty($aulas)): ?>
            <div class="col-span-full text-center bg-white shadow border border-gray-200 rounded-2xl p-12">
                <img
                    src="assets/images/clock.svg"
                    alt="Relógio"
                    class="mx-auto mb-4 w-20 h-20 opacity-70"
                >
                <h2 class="text-xl font-semibold text-gray-800 mb-1">
                    Nenhuma aula disponível 💬
                </h2>
                <p class="text-gray-600 text-sm">
                    Tente outra modalidade ou volte mais tarde.
                </p>
            </div>

        <?php else: ?>

            <!-- EXISTEM AULAS -->
            <?php foreach ($aulas as $aula): ?>
                <div class="flex flex-col justify-between border border-gray-200 bg-white rounded-2xl p-6 
                            shadow-md hover:shadow-lg transition-all duration-200 h-full">

                    <!-- INFORMAÇÕES -->
                    <div class="space-y-3 text-center">
                        <h2 class="text-xl  font-bold text-blue-700">
                            <?= htmlspecialchars($aula["nome_aula"]) ?>
                        </h2>

                        <p class="text-md text-start font-medium text-gray-900">
                            <?= htmlspecialchars($aula["nome_modalidade"]) ?>
                        </p>
                        <p class="text-md text-start font-small text-gray-900">
                            <strong>Instrutor:</strong> <?= htmlspecialchars($aula["nome_funcionario"]) ?>
                        </p>

                        <p class="text-gray-600 text-start text-sm">
                            <strong>Sobre a aula: </strong><br><?= htmlspecialchars($aula["descricao"]) ?>
                        </p>

                        <div class="text-sm text-start text-gray-700 space-y-1">
                            <p><strong>Filial:</strong> <?= htmlspecialchars($aula["nome_filial"]) ?></p>
                            <p><strong>Vagas:</strong> <?= Aulas::getInscritos($aula["id_aula"]) ?>/<?= $aula["quantidade_pessoas"] ?></p>
                            <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($aula["dia_aula"])) ?></p>
                        </div>
                    </div>

                    <!-- BOTÃO AGENDAR -->
                    <form action="aulas/agendar" method="post" class="mt-6">
                        <input type="hidden" name="id_aula" value="<?= $aula['id_aula'] ?>">

                        <?php
                            // bloqueia quem não for aluno
                            $bloqueado = !isset($_SESSION['user_tipo']) || $_SESSION['user_tipo'] !== 'aluno';

                            // verifica se já está agendada para o aluno logado
                            $jaAgendada = false;
                            if (!$bloqueado && !empty($id_aluno)) {
                                $jaAgendada = Aulas::checkAgendado((int)$id_aluno, (int)$aula['id_aula']);
                            }
                        ?>

                        <button
                            type="submit"
                            <?= ($bloqueado || $jaAgendada) ? 'disabled' : '' ?>
                            class="w-full py-3 rounded-xl text-white font-semibold shadow
                                   transition-all duration-200
                                   <?= ($bloqueado || $jaAgendada)
                                        ? 'bg-gray-400 cursor-not-allowed'
                                        : 'bg-blue-600 hover:bg-blue-700' ?>"
                        >
                            <?= $jaAgendada ? 'Já agendada' : 'Agendar Aula' ?>
                        </button>
                    </form>
                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>
</div>
