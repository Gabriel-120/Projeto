<div class="container mx-auto my-12 px-4">

  <div class="mb-6">
      <a href="/" class="inline-flex items-center text-green-600 hover:text-green-700 font-semibold transition-colors duration-200">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
          </svg>
          Voltar ao Início
      </a>
  </div>


    <div class="flex justify-center">
        <div class="w-full md:w-2/3 lg:w-1/2">
            <div class="bg-white shadow-md rounded-lg">
                <div class="p-8">
                    <h2 class="text-2xl font-semibold text-center mb-6">Criar Conta - TechFit</h2>

                    <?php if (isset($_SESSION['flash_message'])): ?>
                        <div class="mb-4 p-4 rounded text-white<?php echo $_SESSION['flash_type'] === 'success' ? 'bg-green-500' : ($_SESSION['flash_type'] === 'error' ? 'bg-red-500' : 'bg-blue-500'); ?>">
                            <?php echo $_SESSION['flash_message']; ?>
                            <button type="button" class="float-right ml-2" onclick="this.parentElement.style.display='none'">&times;</button>
                        </div>
                        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
                    <?php endif; ?>

                    <form method="POST" action="/cadastro" novalidate>
                        <h5 class="text-lg font-medium mb-4 mt-4">Dados Pessoais</h5>

                        <div class="mb-4">
                            <label for="nome" class="block mb-1 font-medium">Nome Completo *</label>
                            <input type="text" id="nome" name="nome" required placeholder="Seu nome completo" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <small class="text-gray-500 text-sm">Mínimo 3 caracteres</small>
                        </div>

                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1 mb-4">
                                <label for="cpf" class="block mb-1 font-medium">CPF *</label>
                                <input type="text" id="cpf" name="cpf" required placeholder="000.000.000-00" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <small class="text-gray-500 text-sm">Apenas números serão aceitos</small>
                            </div>

                            <div class="flex-1 mb-4">
                                <label for="data_nascimento" class="block mb-1 font-medium">Data de Nascimento *</label>
                                <input type="date" id="data_nascimento" name="data_nascimento" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <small class="text-gray-500 text-sm">Mínimo 13 anos</small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="genero" class="block mb-1 font-medium">Gênero *</label>
                            <select id="genero" name="genero" required class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="">Selecione uma opção</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Feminino">Feminino</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>

                        <!-- Seção: Informações de Contato -->
                        <h5 class="text-lg font-medium mb-4 mt-6">Informações de Contato</h5>

                        <div class="mb-4">
                            <label for="email" class="block mb-1 font-medium">Email *</label>
                            <input type="email" id="email" name="email" required placeholder="seu@email.com" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <small class="text-gray-500 text-sm">Será usado para fazer login</small>
                        </div>

                        <div class="mb-4">
                            <label for="telefone" class="block mb-1 font-medium">Telefone *</label>
                            <input type="tel" id="telefone" name="telefone" required placeholder="(11) 98765-4321" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <small class="text-gray-500 text-sm">Mínimo 10 dígitos</small>
                        </div>

                        <div class="mb-4">
                            <label for="endereco" class="block mb-1 font-medium">Endereço *</label>
                            <textarea id="endereco" name="endereco" required placeholder="Rua, número, bairro, cidade" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                            <small class="text-gray-500 text-sm">Mínimo 5 caracteres</small>
                        </div>

                        <h5 class="text-lg font-medium mb-4 mt-6">Segurança</h5>

                        <div class="mb-4">
                            <label for="senha" class="block mb-1 font-medium">Senha *</label>
                            <input type="password" id="senha" name="senha" required placeholder="Mínimo 8 caracteres" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <small class="text-gray-500 text-sm">Deve conter maiúsculas, minúsculas e números</small>
                            <p id="senhaErro" class="mt-1 text-xs text-red-500"></p>
                        </div>

                        <div class="mb-4">
                            <label for="confirmar_senha" class="block mb-1 font-medium">Confirmar Senha *</label>
                            <input type="password" id="confirmar_senha" name="confirmar_senha" required placeholder="Repita sua senha" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <p id="confirmSenhaErro" class="mt-1 text-xs text-red-500"></p>
                        </div>

                        <button type="submit" class="w-full bg-green-500 text-white font-semibold py-2 rounded hover:bg-green-600 transition-colors mb-4">Criar Conta</button>
                    </form>

                    <script>
                        const form = document.querySelector('form');
                        const nomeInput = document.getElementById('nome');
                        const cpfInput = document.getElementById('cpf');
                        const dataNascInput = document.getElementById('data_nascimento');
                        const generoInput = document.getElementById('genero');
                        const emailInput = document.getElementById('email');
                        const telefoneInput = document.getElementById('telefone');
                        const enderecoInput = document.querySelector('textarea[name="endereco"]') || document.getElementById('endereco');
                        const senhaInput = document.getElementById('senha');
                        const confirmSenhaInput = document.getElementById('confirmar_senha');
                        const senhaErro = document.getElementById('senhaErro');
                        const confirmSenhaErro = document.getElementById('confirmSenhaErro');

                        // Formata CPF enquanto digita
                        cpfInput.addEventListener('input', function() {
                            let value = this.value.replace(/\D/g, '');
                            if (value.length > 11) value = value.substring(0, 11);
                            if (value.length > 8) {
                                value = value.substring(0, 3) + '.' + value.substring(3, 6) + '.' + value.substring(6, 9) + '-' + value.substring(9);
                            } else if (value.length > 6) {
                                value = value.substring(0, 3) + '.' + value.substring(3, 6) + '.' + value.substring(6);
                            } else if (value.length > 3) {
                                value = value.substring(0, 3) + '.' + value.substring(3);
                            }
                            this.value = value;
                        });

                        // Formata telefone enquanto digita
                        telefoneInput.addEventListener('input', function() {
                            let value = this.value.replace(/\D/g, '');
                            if (value.length > 11) value = value.substring(0, 11);
                            if (value.length > 7) {
                                value = '(' + value.substring(0, 2) + ') ' + value.substring(2, 7) + '-' + value.substring(7);
                            } else if (value.length > 2) {
                                value = '(' + value.substring(0, 2) + ') ' + value.substring(2);
                            }
                            this.value = value;
                        });

                        // Feedback em tempo real da senha
                        senhaInput.addEventListener('input', function() {
                            senhaErro.textContent = '';
                            confirmSenhaErro.textContent = '';

                            if (this.value.length > 0 && this.value.length < 8) {
                                senhaErro.textContent = 'Senha inválida: mínimo 8 caracteres.';
                            } else if (this.value.length >= 8 && !validarForcaSenha(this.value)) {
                                senhaErro.textContent = 'Senha inválida: use letras maiúsculas, minúsculas e números.';
                            }

                            if (confirmSenhaInput.value.length > 0 && this.value !== confirmSenhaInput.value) {
                                confirmSenhaErro.textContent = 'Senhas não são iguais.';
                            }
                        });

                        confirmSenhaInput.addEventListener('input', function() {
                            confirmSenhaErro.textContent = '';
                            if (this.value.length > 0 && this.value !== senhaInput.value) {
                                confirmSenhaErro.textContent = 'Senhas não são iguais.';
                            }
                        });

                        // Validação ao submeter
                        form.addEventListener('submit', function(e) {
                            e.preventDefault();

                            // Validar Nome
                            if (nomeInput.value.trim().length < 3) {
                                alert('Nome deve ter pelo menos 3 caracteres.');
                                nomeInput.focus();
                                return false;
                            }

                            // Validar CPF
                            const cpfNumeros = cpfInput.value.replace(/\D/g, '');
                            if (cpfNumeros.length !== 11) {
                                alert('CPF deve conter 11 dígitos.');
                                cpfInput.focus();
                                return false;
                            }
                            if (!validarCPF(cpfNumeros)) {
                                alert('CPF inválido.');
                                cpfInput.focus();
                                return false;
                            }

                            // Validar Data de Nascimento
                            if (!dataNascInput.value) {
                                alert('Data de nascimento é obrigatória.');
                                dataNascInput.focus();
                                return false;
                            }
                            if (!validarIdade(dataNascInput.value)) {
                                alert('Você deve ter pelo menos 13 anos para se cadastrar.');
                                dataNascInput.focus();
                                return false;
                            }

                            // Validar Gênero
                            if (!generoInput.value) {
                                alert('Gênero é obrigatório.');
                                generoInput.focus();
                                return false;
                            }

                            // Validar Email
                            if (!validarEmail(emailInput.value)) {
                                alert('Email inválido.');
                                emailInput.focus();
                                return false;
                            }

                            // Validar Telefone
                            const teleNumeros = telefoneInput.value.replace(/\D/g, '');
                            if (teleNumeros.length < 10) {
                                alert('Telefone deve ter pelo menos 10 dígitos.');
                                telefoneInput.focus();
                                return false;
                            }

                            // Validar Endereço
                            if (enderecoInput.value.trim().length < 5) {
                                alert('Endereço deve ter pelo menos 5 caracteres.');
                                enderecoInput.focus();
                                return false;
                            }

                            // Validar Senha
                            if (senhaInput.value.length < 8) {
                                alert('Senha deve ter pelo menos 8 caracteres.');
                                senhaInput.focus();
                                return false;
                            }
                            if (!validarForcaSenha(senhaInput.value)) {
                                alert('Senha deve conter letras maiúsculas, minúsculas e números.');
                                senhaInput.focus();
                                return false;
                            }

                            // Validar Confirmação de Senha
                            if (senhaInput.value !== confirmSenhaInput.value) {
                                alert('As senhas não coincidem.');
                                confirmSenhaInput.focus();
                                return false;
                            }

                            // Tudo OK, submeter formulário
                            form.submit();
                        });

                        // Funções de validação
                        function validarEmail(email) {
                            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                            return regex.test(email);
                        }

                        function validarCPF(cpf) {
                            // Apenas verifica se tem 11 dígitos e não são todos iguais
                            if (cpf.length !== 11) return false;
                            if (/^(\d)\1{10}$/.test(cpf)) return false;
                            return true;
                        }

                        function validarIdade(dataNasc) {
                            const data = new Date(dataNasc);
                            const hoje = new Date();
                            let idade = hoje.getFullYear() - data.getFullYear();
                            const mesAtual = hoje.getMonth();
                            const mesNasc = data.getMonth();
                            if (mesAtual < mesNasc || (mesAtual === mesNasc && hoje.getDate() < data.getDate())) {
                                idade--;
                            }
                            return idade >= 13;
                        }

                        function validarForcaSenha(senha) {
                            const temMaiuscula = /[A-Z]/.test(senha);
                            const temMinuscula = /[a-z]/.test(senha);
                            const temNumero = /[0-9]/.test(senha);
                            return temMaiuscula && temMinuscula && temNumero;
                        }
                    </script>

                    <div class="text-center mt-4">
                        <p class="text-gray-500 mb-2">Já tem conta?</p>
                        <a href="/login" class="w-full inline-block border border-gray-400 text-gray-700 py-2 rounded hover:bg-gray-100 transition-colors">Fazer Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
