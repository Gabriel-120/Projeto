<div class="max-w-4xl mx-auto">
    <h2 class="text-3xl font-bold mb-8 text-gray-800">⚙️ Configurações da Conta</h2>

    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-700">Foto de Perfil</h3>

        <div id="errorMessage" class="hidden mb-4 p-4 rounded border-l-4 border-red-500 bg-red-50">
            <p class="text-red-800 font-medium" id="errorText"></p>
        </div>

        <form method="post" action="/profile?page=configuracao" enctype="multipart/form-data">
            <input type="hidden" name="action" value="change_avatar">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="flex flex-col items-center">
                    <label class="block mb-2 text-gray-600 font-medium w-full">Pré-visualização:</label>
                    <div class="w-full flex justify-center">
                        <img id="previewAvatar"
                             src="<?= htmlspecialchars($user_pfp ?? '/images/upload/pfp/avatar.png') ?>"
                             alt="Pré-visualização do Avatar"
                             class="size-48 rounded-full border-4 border-gray-300 object-cover cursor-pointer hover:opacity-80 transition"
                             title="Clique para selecionar uma nova imagem">
                    </div>
                    <small class="text-gray-500 text-center mt-2">👆 Clique na foto para alterar</small>
                    <input type="file" id="avatarInput" name="avatar" accept="image/jpeg,image/png,image/gif" class="hidden" required>
                </div>

                <div class="flex flex-col justify-center">
                    <label for="avatarInput2" class="block mb-2 text-gray-600 font-medium">Seletor de Arquivo:</label>
                    <input type="file" id="avatarInput2" name="avatar" accept="image/jpeg,image/png,image/gif"
                           class="border border-gray-300 p-2 w-full rounded text-gray-600 mb-4"
                           required>
                    <small class="text-gray-500 block mb-4">
                        ✓ Formatos: JPG, PNG, GIF<br>
                        ✓ Tamanho máximo: 5MB
                    </small>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded font-medium transition">
                        ✅ Atualizar Avatar
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const previewImg = document.getElementById('previewAvatar');
        const fileInput = document.getElementById('avatarInput');
        const fileInput2 = document.getElementById('avatarInput2');
        const errorMessage = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');

        previewImg.addEventListener('click', () => {
            fileInput.click();
        });


        function showError(message) {
            errorText.textContent = message;
            errorMessage.classList.remove('hidden');
            setTimeout(() => {
                errorMessage.classList.add('hidden');
            }, 5000);
        }

        // Função para validar e fazer preview
        function handleFileChange(input) {
            const file = input.files[0];

            if (file) {
                // Valida o tipo de arquivo
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    showError('❌ Formato inválido! Apenas imagens JPG, PNG ou GIF são permitidas.');
                    input.value = '';
                    fileInput.value = '';
                    fileInput2.value = '';
                    previewImg.src = "<?= htmlspecialchars($user_pfp ?? '/images/upload/pfp/avatar.png') ?>";
                    return;
                }

                // Valida o tamanho (5MB)
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    showError(`❌ Arquivo muito grande! Tamanho: ${fileSizeMB}MB (máximo: 5MB). Por favor, selecione uma imagem menor.`);
                    input.value = '';
                    fileInput.value = '';
                    fileInput2.value = '';
                    previewImg.src = "<?= htmlspecialchars($user_pfp ?? '/images/upload/pfp/avatar.png') ?>";
                    return;
                }

                // Limpa mensagem de erro e cria a pré-visualização
                errorMessage.classList.add('hidden');
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    // Sincroniza ambos os inputs
                    if (input === fileInput) {
                        fileInput2.files = input.files;
                    } else {
                        fileInput.files = input.files;
                    }
                };
                reader.readAsDataURL(file);
            }
        }

        // Event listeners para ambos os inputs
        fileInput.addEventListener('change', function() {
            handleFileChange(this);
        });

        fileInput2.addEventListener('change', function() {
            handleFileChange(this);
        });
    </script>

    <!-- Seção de Dados Pessoais -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-700">Dados Pessoais</h3>
        <form method="post" action="/profile?page=configuracao">
            <input type="hidden" name="action" value="update_profile">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block mb-2 text-gray-600 font-medium">Nome Completo:</label>
                    <input type="text" name="nome" value="<?php echo htmlspecialchars($nome) ?>"
                           class="border border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500"
                           required>
                </div>

                <div>
                    <label class="block mb-2 text-gray-600 font-medium">Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email) ?>"
                           class="border border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500"
                           required>
                </div>
            </div>

            <?php if (strtolower($tipo) === 'aluno'): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block mb-2 text-gray-600 font-medium">Telefone:</label>
                        <input type="tel" name="telefone" value="<?php echo htmlspecialchars($telefone ?? '') ?>"
                               placeholder="(11) 9 9999-9999"
                               class="border border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block mb-2 text-gray-600 font-medium">Gênero:</label>
                        <select name="genero" class="border border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500">
                            <option value="">Selecione...</option>
                            <option value="Masculino"                                                      <?php echo($genero ?? '') === 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                            <option value="Feminino"                                                     <?php echo($genero ?? '') === 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                            <option value="Outro"                                                  <?php echo($genero ?? '') === 'Outro' ? 'selected' : '' ?>>Outro</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block mb-2 text-gray-600 font-medium">Endereço:</label>
                    <textarea name="endereco"
                              class="border border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500 resize-none"
                              rows="3"><?php echo htmlspecialchars($endereco ?? '') ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 bg-gray-50 p-4 rounded">
                    <div>
                        <label class="block mb-2 text-gray-600 font-medium">CPF:</label>
                        <input type="text" value="<?php echo htmlspecialchars($cpf ?? '') ?>"
                               class="border border-gray-300 p-3 w-full rounded bg-gray-100"
                               disabled>
                        <small class="text-gray-500">Não pode ser alterado</small>
                    </div>

                    <div>
                        <label class="block mb-2 text-gray-600 font-medium">Data de Nascimento:</label>
                        <input type="date" value="<?php echo htmlspecialchars($data_nascimento ?? '') ?>"
                               class="border border-gray-300 p-3 w-full rounded bg-gray-100"
                               disabled>
                        <small class="text-gray-500">Não pode ser alterada</small>
                    </div>
                </div>
            <?php endif; ?>

            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-medium transition">
                ✅ Salvar Alterações
            </button>
        </form>
    </div>

    <!-- Seção de Segurança -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-700">🔒 Segurança</h3>
        <form method="post" action="/profile?page=configuracao">
            <input type="hidden" name="action" value="change_password">

            <div class="grid grid-cols-1 gap-4 mb-4">
                <div>
                    <label class="block mb-2 text-gray-600 font-medium">Senha Atual:</label>
                    <input type="password" name="senha_atual"
                           class="border border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500"
                           required>
                </div>

                <div>
                    <label class="block mb-2 text-gray-600 font-medium">Nova Senha:</label>
                    <input type="password" id="senha_nova" name="senha_nova"
                           class="border border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500"
                           required>
                    <small class="text-gray-500">Mínimo 8 caracteres. Deve conter maiúsculas, minúsculas e números.</small>
                    <p id="senhaNovaErro" class="mt-1 text-xs text-red-500"></p>
                </div>

                <div>
                    <label class="block mb-2 text-gray-600 font-medium">Confirmar Nova Senha:</label>
                    <input type="password" id="senha_confirmacao" name="senha_confirmacao"
                           class="border border-gray-300 p-3 w-full rounded focus:outline-none focus:border-blue-500"
                           required>
                    <p id="senhaConfirmErro" class="mt-1 text-xs text-red-500"></p>
                </div>
            </div>

            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded font-medium transition">
                🔑 Alterar Senha
            </button>
        </form>
    </div>

    <!-- Informações Úteis -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded">
        <h4 class="font-semibold text-blue-900 mb-2">💡 Dicas de Segurança</h4>
        <ul class="text-blue-800 text-sm space-y-1">
            <li>✓ Use uma senha forte com letras, números e caracteres especiais</li>
            <li>✓ Nunca compartilhe sua senha com ninguém</li>
            <li>✓ Altere sua senha regularmente</li>
            <li>✓ Verifique se seu email está correto para receber notificações</li>
        </ul>
    </div>

    <script>
        // Validação em tempo real da senha na seção de Segurança
        const changePassAction = document.querySelector('input[name="action"][value="change_password"]');
        if (changePassAction) {
            const formSenha = changePassAction.closest('form');
            const senhaNovaInput = formSenha.querySelector('input[name="senha_nova"]');
            const senhaConfirmInput = formSenha.querySelector('input[name="senha_confirmacao"]');
            const senhaNovaErro = document.getElementById('senhaNovaErro');
            const senhaConfirmErro = document.getElementById('senhaConfirmErro');

            function validarForcaSenhaFront(senha) {
                const temMaiuscula = /[A-Z]/.test(senha);
                const temMinuscula = /[a-z]/.test(senha);
                const temNumero = /[0-9]/.test(senha);
                return temMaiuscula && temMinuscula && temNumero;
            }

            senhaNovaInput.addEventListener('input', function () {
                senhaNovaErro.textContent = '';
                senhaConfirmErro.textContent = '';

                if (this.value.length > 0 && this.value.length < 8) {
                    senhaNovaErro.textContent = 'Senha inválida: mínimo 8 caracteres.';
                } else if (this.value.length >= 8 && !validarForcaSenhaFront(this.value)) {
                    senhaNovaErro.textContent = 'Senha inválida: use letras maiúsculas, minúsculas e números.';
                }

                if (senhaConfirmInput.value.length > 0 && this.value !== senhaConfirmInput.value) {
                    senhaConfirmErro.textContent = 'Senhas não são iguais.';
                }
            });

            senhaConfirmInput.addEventListener('input', function () {
                senhaConfirmErro.textContent = '';
                if (this.value.length > 0 && this.value !== senhaNovaInput.value) {
                    senhaConfirmErro.textContent = 'Senhas não são iguais.';
                }
            });

            formSenha.addEventListener('submit', function (e) {
                senhaNovaErro.textContent = '';
                senhaConfirmErro.textContent = '';

                const nova = senhaNovaInput.value;
                const conf = senhaConfirmInput.value;

                if (nova.length < 8) {
                    e.preventDefault();
                    senhaNovaErro.textContent = 'Senha inválida: mínimo 8 caracteres.';
                    senhaNovaInput.focus();
                    return;
                }

                if (!validarForcaSenhaFront(nova)) {
                    e.preventDefault();
                    senhaNovaErro.textContent = 'Senha inválida: use letras maiúsculas, minúsculas e números.';
                    senhaNovaInput.focus();
                    return;
                }

                if (nova !== conf) {
                    e.preventDefault();
                    senhaConfirmErro.textContent = 'Senhas não são iguais.';
                    senhaConfirmInput.focus();
                }
            });
        }
    </script>
</div>
