<?php
/** @var array $avisos */
/** @var array|null $avisoSelecionado */
/** @var array $nomeTipo */
/** @var array $erros */
/** @var ?string $sucesso */
/** @var bool $isFuncionario */
require_once __DIR__ . "/../partials/nav.php";
?>

<div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 py-4">

    <!-- LISTA DE AVISOS -->
    <div class="md:col-span-1">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Comunicados</h2>

        <?php if(!empty($avisos)): ?>
            <?php foreach ($avisos as $aviso): ?>
                <a href="/comunicados?id=<?php echo (int)$aviso['id_alerta']; ?>"
                   class="block bg-white border border-gray-200 rounded-xl p-4 mb-3 shadow-sm hover:shadow-md transition-shadow">
                    <h3 class="font-semibold text-gray-900 text-lg">
                        <?php echo htmlspecialchars($aviso['titulo']); ?>
                    </h3>

                    <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                        <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 text-[10px] uppercase">
                            <?php echo htmlspecialchars($nomeTipo[$aviso['tipo']] ?? $aviso['tipo']); ?>
                        </span>
                        • <?php echo htmlspecialchars($aviso['data_criacao']); ?>
                    </p>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if($isFuncionario): ?>
            <a href="/adm/comunicados"
               class="block mt-4 text-center border rounded-xl py-2 bg-gray-50 hover:bg-gray-100 transition text-sm text-gray-700">
                ➕ Criar novo comunicado
            </a>
        <?php endif; ?>
    </div>

    <!-- DETALHES DO AVISO SELECIONADO -->
    <div class="md:col-span-2">
        <?php if (!empty($erros)): ?>
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-sm shadow">
                <ul class="list-disc ml-4">
                    <?php foreach ($erros as $erro): ?>
                        <li><?php echo htmlspecialchars($erro); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (!empty($sucesso)): ?>
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 p-3 rounded-lg text-sm shadow">
                <?php echo htmlspecialchars($sucesso); ?>
            </div>
        <?php endif; ?>

        <?php if ($avisoSelecionado): ?>
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-lg">

                <div class="mb-4">
                    <h2 class="text-3xl font-bold text-gray-900">
                        <?php echo htmlspecialchars($avisoSelecionado['titulo']); ?>
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Autor: <em><?php echo htmlspecialchars($avisoSelecionado['nome_funcionario']); ?></em>
                        • Criado em <?php echo htmlspecialchars($avisoSelecionado['data_criacao']); ?>
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        Expira: <?php echo htmlspecialchars($avisoSelecionado['expira']); ?>
                    </p>
                </div>

                <div class="prose prose-gray max-w-none text-gray-800 mb-6 whitespace-pre-line">
                    <?php echo nl2br(htmlspecialchars($avisoSelecionado['conteudo'])); ?>
                </div>

                <!-- ANEXO -->
                <?php if (!empty($avisoSelecionado['anexo_path'])): ?>
                    <div class="mb-6">
                        <p class="text-sm font-medium text-gray-700 mb-2">Anexo:</p>
                        <a href="<?php echo htmlspecialchars($avisoSelecionado['anexo_path']); ?>" target="_blank">
                            <img src="<?php echo htmlspecialchars($avisoSelecionado['anexo_path']); ?>"
                                 class="max-h-72 rounded-lg shadow border object-cover hover:opacity-90 transition" />
                        </a>
                    </div>
                <?php endif; ?>

                <!-- SEÇÃO ADMIN -->
                <?php if ($isFuncionario): ?>
                    <hr class="my-6">

                    <details class="mb-3 group">
                        <summary
                            class="cursor-pointer text-sm text-blue-700 font-medium group-open:text-blue-900">
                            ✏️ Editar comunicado
                        </summary>

                        <form method="post" enctype="multipart/form-data" class="mt-4 space-y-4">

                            <input type="hidden" name="id_alerta" value="<?php echo (int)$avisoSelecionado['id_alerta']; ?>">

                            <!-- TÍTULO -->
                            <div>
                                <label class="block text-sm font-semibold mb-1">Título</label>
                                <input type="text"
                                       name="titulo"
                                       value="<?php echo htmlspecialchars($avisoSelecionado['titulo']); ?>"
                                       class="w-full border rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            </div>

                            <!-- TIPO -->
                            <div>
                                <label class="block text-sm font-semibold mb-1">Tipo</label>
                                <select name="tipo"
                                        class="w-full border rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                                    <?php foreach ($nomeTipo as $valor => $label): ?>
                                        <option value="<?php echo $valor; ?>"
                                            <?php echo $avisoSelecionado['tipo'] === $valor ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- EXPIRA -->
                            <div>
                                <label class="block text-sm font-semibold mb-1">Expira</label>
                                <input type="date"
                                       name="expira"
                                       value="<?php echo htmlspecialchars($avisoSelecionado['expira']); ?>"
                                       class="w-full border rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                            </div>

                            <!-- CONTEÚDO -->
                            <div>
                                <label class="block text-sm font-semibold mb-1">Conteúdo</label>
                                <textarea name="conteudo" rows="6"
                                          class="w-full border rounded-lg p-3 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none"><?php echo htmlspecialchars($avisoSelecionado['conteudo']); ?></textarea>
                            </div>

                            <!-- PREVIEW -->
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-700 mb-1">Pré-visualização:</p>
                                <img id="preview-image"
                                     class="max-h-64 rounded-lg border shadow-sm"
                                     src="<?php echo htmlspecialchars($avisoSelecionado['anexo_path'] ?? ''); ?>">
                            </div>

                            <!-- UPLOAD -->
                            <div>
                                <label class="block text-sm font-semibold mb-1">Alterar anexo</label>
                                <input type="file" accept="image/*" name="anexo"
                                       onchange="previewAnexo(event)"
                                       class="w-full text-sm file:bg-blue-600 file:text-white file:px-4 file:py-2 file:rounded-lg file:border-0 hover:file:bg-blue-700 cursor-pointer">
                            </div>

                            <!-- AÇÕES -->
                            <div class="flex gap-3 pt-3">
                                <button type="submit" name="acao" value="edit"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                                    Salvar alterações
                                </button>

                                <button type="submit" name="acao" value="delete"
                                        onclick="return confirm('Tem certeza que deseja excluir este comunicado?');"
                                        class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition">
                                    Excluir
                                </button>
                            </div>
                        </form>
                    </details>
                <?php endif; ?>

            </div>
        <?php else: ?>
            <p class="text-gray-500">Selecione um comunicado para ver os detalhes.</p>
        <?php endif; ?>
    </div>
</div>
