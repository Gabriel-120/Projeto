<?php require_once __DIR__. "/../partials/nav.php"?>
<div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md border">
    <h2 class="text-xl font-semibold mb-6 text-gray-700">Criar Novo Comunicado</h2>
 <form action="/adm/comunicados" method="post" class="flex flex-col space-y-4" enctype="multipart/form-data">
    <div>
        <label for="titulo" class="block text-gray-700 font-medium mb-2">
            Título
        </label>
        <input
            type="text"
            name="titulo"
            id="titulo"
            class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
            placeholder="Digite o título do comunicado..."
            required
        >
    </div>

    <div>
        <label class="block text-gray-700 font-medium mb-2" for="tipo">Tipo de Aviso</label>
        <select name="tipo" id="tipo" class="border border-gray-300 rounded-lg p-2">
            <?php foreach ($avisoTipos as $tipo): ?>
                <option value="<?php echo htmlspecialchars($tipo)?>">
                    <?php echo htmlspecialchars($nomeTipo[$tipo])?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="conteudo" class="block text-gray-700 font-medium mb-2">
            Conteúdo
        </label>
        <textarea
            name="conteudo"
            id="conteudo"
            rows="8"
            class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
            placeholder="Escreva o conteúdo do comunicado aqui..."
            required
        ></textarea>
    </div>

  <div>
    <label for="anexo" class="block text-gray-700 font-medium mb-2">
        Anexo (opcional)
    </label>
    <input
        type="file"
        name="anexo"
        id="anexo"
        accept="image/*"
        class="block w-full text-sm text-gray-700
               file:mr-4 file:py-2 file:px-4
               file:rounded-lg file:border-0
               file:text-sm file:font-semibold
               file:bg-blue-50 file:text-blue-700
               hover:file:bg-blue-100"
    >

    
    <div id="preview-container" class="mt-3 hidden">
        <p class="text-sm text-gray-600 mb-1">Pré-visualização:</p>
        <img id="preview-image" class="max-h-48 rounded border" alt="Pré-visualização do anexo">
    </div>
</div>


    <button
        type="submit"
        class="bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700 transition-all">
        Enviar Comunicado
    </button>
</form>

</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputFile = document.getElementById('anexo');
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-image');

    if (!inputFile) return;

    inputFile.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) {
            previewContainer.classList.add('hidden');
            previewImage.src = '';
            return;
        }

        
        if (!file.type.startsWith('image/')) {
            previewContainer.classList.add('hidden');
            previewImage.src = '';
            alert('Por enquanto, só é possível pré-visualizar imagens.');
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            previewImage.src = e.target.result;
            previewContainer.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    });
});
</script>
