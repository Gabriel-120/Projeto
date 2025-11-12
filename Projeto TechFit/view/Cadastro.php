<?php
// Incluir controller e modelo
require_once __DIR__ . '/../controller/controller.php';
// require_once __DIR__ . '/../model/cadastro.php';
// require_once __DIR__ . '/../model/cadastroDAO.php';

$mensagem_erro = '';
$sucesso = false;

// Verificar se o formulário foi submetido via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coletar dados do formulário
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $cpf = isset($_POST['cpf']) ? trim($_POST['cpf']) : '';
    $data_nascimento = isset($_POST['nascimento']) ? trim($_POST['nascimento']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Validar campos obrigatórios
    if (empty($nome) || empty($email) || empty($cpf) || empty($data_nascimento) || empty($password)) {
        $mensagem_erro = 'Todos os campos são obrigatórios.';
    } 
    // Validar confirmação de senha
    elseif ($password !== $confirm_password) {
        $mensagem_erro = 'As senhas não conferem.';
    }
    // Validar email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem_erro = 'Email inválido.';
    }
    // Validar CPF (formato básico)
    elseif (!preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $cpf)) {
        $mensagem_erro = 'CPF deve estar no formato: 000.000.000-00';
    }
    else {
        // Se tudo estiver válido, criar o cadastro
        try {
            $dao = new cadastroDAO();
            $cadastros = $dao->lerCadastro();
            
            // Gerar próximo ID
            if (empty($cadastros)) {
                $id = 1;
            } else {
                $ultimo = end($cadastros);
                $id = $ultimo->getId() + 1;
            }
            
            // Hash de senha (segurança)
            $senha_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Criar objeto e salvar
            $novo_cadastro = new cadastro($id, $nome, $email, $cpf, $data_nascimento, $senha_hash);
            $dao->CriarCadastro($novo_cadastro);
            
            $sucesso = true;
            // Redirecionar para Login após 2 segundos
            header('Refresh: 2; url=Login.html');
        } catch (Exception $e) {
            $mensagem_erro = 'Erro ao criar cadastro: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Login_Cadastro.css">
    <title>TechFit - Cadastro</title>
</head>
<body>
    <div class="container">
        <main>
            <!-- Form único para cadastro: action aponta para este mesmo arquivo (Cadastro.php) -->
            <div class="cadastro">
                <!-- Exibir mensagem de erro se houver -->
                <?php if (!empty($mensagem_erro)): ?>
                    <div style="background-color: #ff6b6b; color: white; padding: 12px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-size: 14px;">
                        <?php echo htmlspecialchars($mensagem_erro); ?>
                    </div>
                <?php endif; ?>

                <!-- Exibir mensagem de sucesso -->
                <?php if ($sucesso): ?>
                    <div style="background-color: #51cf66; color: white; padding: 12px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-size: 14px;">
                        ✓ Cadastro realizado com sucesso! Redirecionando para login...
                    </div>
                <?php else: ?>
                    <form action="Cadastro.php" method="POST" id="registerForm" novalidate>
                    <section>
                        <img src="imagens/logo2.png" alt="Logo">
                    </section>
                    <section class="cadastro-input" id="cadastro-section">
                            <label for="nome">Digite seu nome:</label>
                            <input type="text" name="nome" id="nome" placeholder="Nome Completo" required>
                            <label for="email">Digite seu email:</label>
                            <input type="email" name="email" id="email" placeholder="exemplo@gmail.com" required>
                            <label for="cpf">Digite seu CPF:</label>
                            <input type="text" name="cpf" id="cpf" placeholder="EX: 123.456.789-00" required>
                            <label for="data-nascimento">Data de Nascimento:</label>
                            <input type="date" name="nascimento" id="data-nascimento" required>

                            <!-- Botões movidos para dentro da seção de cadastro para serem escondidos junto -->
                            <div class="redirect">
                                <button type="button" onclick="validarPrimeiraEtapa()" class="primary">Próximo</button>
                                <button type="button" onclick="tenhoconta()" class="secondary">Voltar</button>
                            </div>
                        </section>

                    <!-- Seção de senha - inicialmente escondida, faz parte do mesmo form -->
                    <section id="senha" class="senha" style="display:none;">
                        <section class="senha-input">
                            <label for="senha">Digite sua senha:</label>
                            <input type="password" name="password" id="senha-input" placeholder="Digite sua senha" required>
                            <label for="conf-senha">Confirme sua senha:</label>
                            <input type="password" name="confirm_password" id="conf-senha" placeholder="Confirme sua senha" required>
                        </section>
                        <section class="redirect">
                            <!-- submit envia todos os dados do form para o backend -->
                            <button type="submit" class="primary">Cadastrar</button>
                            <button type="button" onclick="mostrarsecao('cadastro')" class="secondary">Voltar</button>
                        </section>
                    </section>
                    </form>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <footer></footer>

    <script>
        // Alternador de etapas: quando o usuário clica em "Próximo" esconde a primeira parte e mostra a parte de senha.
        document.addEventListener('DOMContentLoaded', function () {
            const step1 = document.getElementById('cadastro-section');
            const step2 = document.getElementById('senha');
            if (!step1 || !step2) return;

            // estado inicial
            step1.style.display = 'block';
            step2.style.display = 'none';

            // botão Voltar (na seção de senha)
            const backBtns = step2.querySelectorAll('.redirect button.secondary');
            backBtns.forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    step2.style.display = 'none';
                    step1.style.display = 'block';
                    const nomeInput = document.getElementById('nome');
                    if (nomeInput) nomeInput.focus();
                });
            });
        });

        function validarPrimeiraEtapa() {
            const nome = document.getElementById('nome').value.trim();
            const email = document.getElementById('email').value.trim();
            const cpf = document.getElementById('cpf').value.trim();
            const data = document.getElementById('data-nascimento').value.trim();

            // Validar campos
            if (!nome || !email || !cpf || !data) {
                alert('Todos os campos são obrigatórios.');
                return;
            }

            // Validar email (formato básico)
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Email inválido.');
                return;
            }

            // Validar CPF (formato)
            const cpfRegex = /^\d{3}\.\d{3}\.\d{3}-\d{2}$/;
            if (!cpfRegex.test(cpf)) {
                alert('CPF deve estar no formato: 000.000.000-00');
                return;
            }

            // Se passou em todas as validações, avançar para seção de senha
            mostrarsecao('senha');
        }

        function mostrarsecao(secao) {
            const step1 = document.getElementById('cadastro-section');
            const step2 = document.getElementById('senha');
            if (!step1 || !step2) return;

            if (secao === 'senha') {
                step1.style.display = 'none';
                step2.style.display = 'block';
                const senhaInput = document.getElementById('senha-input');
                if (senhaInput) senhaInput.focus();
            } else if (secao === 'cadastro') {
                step2.style.display = 'none';
                step1.style.display = 'block';
                const nomeInput = document.getElementById('nome');
                if (nomeInput) nomeInput.focus();
            }
        }

        function tenhoconta() {
            window.location.href = "Login.html";
        }

        // Validar confirmação de senha no submit
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registerForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const password = document.getElementById('senha-input').value;
                    const confirmPassword = document.getElementById('conf-senha').value;
                    
                    if (password !== confirmPassword) {
                        e.preventDefault();
                        alert('As senhas não conferem!');
                        document.getElementById('conf-senha').focus();
                        return false;
                    }
                    
                    if (password.length < 6) {
                        e.preventDefault();
                        alert('A senha deve ter pelo menos 6 caracteres.');
                        document.getElementById('senha-input').focus();
                        return false;
                    }
                });
            }
        });
    </script>
</body>
</html>