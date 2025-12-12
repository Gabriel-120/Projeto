<?php
/** @var array $avisos */
/** @var array|null $avisoSelecionado */
/** @var array $nomeTipo */
/** @var array $erros */
/** @var ?string $sucesso */
/** @var bool $isFuncionario */
require_once __DIR__ . "/../partials/nav.php";
?>

<div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-1">
        <h2 class="text-xl font-semibold mb-4 text-gray-700">Comunicados</h2>
        <?php if(!empty($avisos)):?>
        <?php foreach ($avisos as $aviso): ?>
            <a href="/comunicados?id=<?php echo (int) $aviso['id_alerta']; ?>"
               class="block text-decoration-none text-black border rounded-lg p-3 mb-3 hover:bg-gray-50 transition">
                <h3 class="font-semibold text-gray-800">
                    <?php echo htmlspecialchars($aviso['titulo']); ?>
                </h3>
                <p class="text-xs text-gray-500">
                    <?php echo htmlspecialchars($nomeTipo[$aviso['tipo']] ?? $aviso['tipo']); ?>
                    •<?php echo htmlspecialchars($aviso['data_criacao']); ?>
                </p>
            </a>
        <?php endforeach; ?>

        <?php endif;?>
        
        <?php if($isFuncionario):?>
            <a href="/adm/comunicados" class="text-center text-decoration-none text-black font-light italic block border rounded-lg p-3 mb-3 hover:bg-gray-50 transition">Criar novo comunicado</a>
        <?php endif;?>
    </div>

    <div class="md:col-span-2">
        <?php if (! empty($erros)): ?>
            <div class="mb-4 bg-red-100 text-red-700 p-3 rounded text-sm">
                <ul class="list-disc ml-4">
                    <?php foreach ($erros as $erro): ?>
                        <li><?php echo htmlspecialchars($erro); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (! empty($sucesso)): ?>
            <div class="mb-4 bg-green-100 text-green-700 p-3 rounded text-sm">
                <?php echo htmlspecialchars($sucesso); ?>
            </div>
        <?php endif; ?>
        <?php if ($avisoSelecionado): ?>
            <div class="bg-white border rounded-lg p-6 shadow-sm">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800 mb-1">
                            <?php echo htmlspecialchars($avisoSelecionado['titulo']); ?>
                        </h2>
                        <p class="text-sm text-gray-500">
                            Autor - <em><?php echo htmlspecialchars($avisoSelecionado['nome_funcionario']); ?></em>
                        </p>
                        <p class="text-sm text-gray-500">
                            <?php echo htmlspecialchars($nomeTipo[$avisoSelecionado['tipo']] ?? $avisoSelecionado['tipo']); ?>
                            • Criado em: <?php echo htmlspecialchars($avisoSelecionado['data_criacao']); ?>
                        </p>
                        <p class="text-xs text-gray-400">
                            Expira em: <?php echo htmlspecialchars($avisoSelecionado['expira']); ?>
                        </p>
                    </div>
                </div>

                <div class="prose max-w-none text-gray-800 mb-6 whitespace-pre-line">
                    <?php echo nl2br(htmlspecialchars($avisoSelecionado['conteudo'])); ?>
                </div>

                
                <?php if (!empty($avisoSelecionado['anexo_path'])): ?>
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-1">Anexo:</p>
                        <a href="<?php echo htmlspecialchars($avisoSelecionado['anexo_path']); ?>"
                           target="_blank" class="inline-block">
                            <img src="<?php echo htmlspecialchars($avisoSelecionado['anexo_path']); ?>"
                                 alt="Anexo" class="max-h-64 rounded border">
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ($isFuncionario): ?>
                    <hr class="my-4">

                    <details class="mb-4">
                        <summary class="cursor-pointer text-sm text-blue-700">Editar comunicado</summary>

                        <form method="post" class="mt-3 space-y-3" enctype="multipart/form-data">
                            <input type="hidden" name="id_alerta"
                                   value="<?php echo (int) $avisoSelecionado['id_alerta']; ?>">

                            <div>
                                <label class="block text-sm font-medium mb-1" for="titulo">Título</label>
                                <input type="text"
                                       id="titulo"
                                       name="titulo"
                                       value="<?php echo htmlspecialchars($avisoSelecionado['titulo']); ?>"
                                       class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1" for="tipo">Tipo</label>
                                <select id="tipo" name="tipo" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                                    <?php foreach ($nomeTipo as $valor => $label): ?>
                                        <option value="<?php echo htmlspecialchars($valor); ?>"
                                            <?php echo $avisoSelecionado['tipo'] === $valor ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1" for="expira">Data de expiração</label>
                                <input type="date"
                                       id="expira"
                                       name="expira"
                                       value="<?php echo htmlspecialchars($avisoSelecionado['expira']); ?>"
                                       class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1" for="conteudo">Conteúdo</label>
                                <textarea id="conteudo"
                                          name="conteudo"
                                          rows="6"
                                          class="w-full border border-gray-300 rounded-lg p-2 text-sm"><?php echo htmlspecialchars($avisoSelecionado['conteudo']); ?></textarea>
                            </div>

                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-1">Anexo atual:</p>
                                <img id="preview-image"
                                     class="max-h-64 rounded border"
                                     src="<?php echo htmlspecialchars($avisoSelecionado['anexo_path'] ?? ''); ?>"
                                     alt="Pré-visualização do anexo">
                            </div>

                            <div>
                                <label for="anexo" class="block text-sm font-medium mb-1">Alterar anexo</label>
                                <input type="file"
                                       name="anexo"
                                       id="anexo"
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-700
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-lg file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-blue-50 file:text-blue-700
                                              hover:file:bg-blue-100"
                                       onchange="previewAnexo(event)">
                            </div>

                            <div class="flex items-center gap-3">
                                <button type="submit"
                                        name="acao"
                                        value="edit"
                                        class="bg-blue-600 text-white text-sm py-1.5 px-4 rounded hover:bg-blue-700">
                                    Salvar alterações
                                </button>

                                <button type="submit"
                                        name="acao"
                                        value="delete"
                                        class="bg-red-600 text-white text-sm py-1.5 px-4 rounded hover:bg-red-700"
                                        onclick="return confirm('Tem certeza que deseja excluir este comunicado?');">
                                    Excluir
                                </button>
                            </div>
                        </form>
                    </details>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500">Selecione um comunicado na lista para ver os detalhes.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function previewAnexo(event) {
    const previewImage = document.getElementById('preview-image');
    const file = event.target.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result; 
        };
        reader.readAsDataURL(file);
    } else {
        
        previewImage.src = "<?php echo htmlspecialchars($avisoSelecionado['anexo_path'] ?? ''); ?>";
    }
}
</script>
