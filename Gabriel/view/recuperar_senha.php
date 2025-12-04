<?php
session_start();

require_once __DIR__ . '/../controller/controller.php';

$mensagem_erro = '';
$mensagem_sucesso = '';
$etapa = 'verificacao'; // 'verificacao' ou 'reset'
$token = '';

// Se chegou com token, mostrar formulário de nova senha
if (isset($_GET['token'])) {
    $token = trim($_GET['token']);
    $controller = new CadastroController();
    $resultado = $controller->verificarToken($token);
    
    if ($resultado['sucesso']) {
        $etapa = 'reset';
    } else {
        $mensagem_erro = $resultado['erro'];
    }
}

// Processar solicitação de recuperação (Etapa 1)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['etapa']) && $_POST['etapa'] === 'verificacao') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $cpf = isset($_POST['cpf']) ? trim($_POST['cpf']) : '';

    if (empty($email) || empty($cpf)) {
        $mensagem_erro = 'Email e CPF são obrigatórios.';
    } else {
        $controller = new CadastroController();
        $resultado = $controller->solicitarRecuperacao($email, $cpf);

        if ($resultado['sucesso']) {
            $mensagem_sucesso = 'Verificação bem-sucedida! Um link de recuperação foi gerado. Você será redirecionado...';
            $token = $resultado['token'];
            // Redirecionar para a página com o token após 2 segundos
            header('Refresh: 2; url=recuperar_senha.php?token=' . urlencode($token));
        } else {
            $mensagem_erro = $resultado['erro'];
        }
    }
}

// Processar redefinição de senha (Etapa 2)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['etapa']) && $_POST['etapa'] === 'reset') {
    $token = isset($_POST['token']) ? trim($_POST['token']) : '';
    $nova_senha = isset($_POST['nova_senha']) ? $_POST['nova_senha'] : '';
    $confirma_senha = isset($_POST['confirma_senha']) ? $_POST['confirma_senha'] : '';

    if (empty($token) || empty($nova_senha) || empty($confirma_senha)) {
        $mensagem_erro = 'Todos os campos são obrigatórios.';
    } else {
        $controller = new CadastroController();
        $resultado = $controller->redefinirSenha($token, $nova_senha, $confirma_senha);

        if ($resultado['sucesso']) {
            $mensagem_sucesso = $resultado['mensagem'] . ' Você será redirecionado para o login...';
            header('Refresh: 2; url=Login.php');
        } else {
            $mensagem_erro = $resultado['erro'];
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
    <title>TechFit - Recuperar Senha</title>
</head>
<body>
    <div class="container">
        <main>
            <div class="recuperar-senha">
                <!-- Logo -->
                <section class="logo-section">
                    <img src="imagens/logo2.png" alt="Logo TechFit" class="logo">
                </section>

                <!-- Exibir mensagem de erro se houver -->
                <?php if (!empty($mensagem_erro)): ?>
                    <div class="alert alert-error">
                        <?php echo htmlspecialchars($mensagem_erro); ?>
                    </div>
                <?php endif; ?>

                <!-- Exibir mensagem de sucesso se houver -->
                <?php if (!empty($mensagem_sucesso)): ?>
                    <div class="alert alert-success">
                        ✓ <?php echo htmlspecialchars($mensagem_sucesso); ?>
                    </div>
                <?php else: ?>
                    <!-- Etapa 1: Verificação de Email e CPF -->
                    <?php if ($etapa === 'verificacao'): ?>
                        <form action="recuperar_senha.php" method="POST" id="verificacaoForm">
                            <input type="hidden" name="etapa" value="verificacao">
                            
                            <h2>Recuperar Senha</h2>
                            <p class="form-description">Digite seu email e CPF para verificarmos sua identidade.</p>

                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" name="email" id="email" placeholder="Digite seu email" required>
                            </div>

                            <div class="form-group">
                                <label for="cpf">CPF *</label>
                                <input type="text" name="cpf" id="cpf" placeholder="000.000.000-00" required>
                            </div>

                            <div class="recuperar-buttons">
                                <button type="submit" class="btn-primary">Verificar</button>
                                <button type="button" class="btn-secondary" onclick="voltarLogin()">Voltar</button>
                            </div>

                            <p class="form-link-center">
                                <a href="Login.php">Fazer login</a> | 
                                <a href="Cadastro.php">Criar conta</a>
                            </p>
                        </form>

                    <!-- Etapa 2: Nova Senha -->
                    <?php else: ?>
                        <form action="recuperar_senha.php" method="POST" id="resetForm">
                            <input type="hidden" name="etapa" value="reset">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                            
                            <h2>Redefinir Senha</h2>
                            <p class="form-description">Digite sua nova senha e confirme.</p>

                            <div class="form-group">
                                <label for="nova_senha">Nova Senha *</label>
                                <input type="password" name="nova_senha" id="nova_senha" placeholder="Digite sua nova senha" required>
                                <small class="form-hint">Mínimo 6 caracteres</small>
                            </div>

                            <div class="form-group">
                                <label for="confirma_senha">Confirmar Senha *</label>
                                <input type="password" name="confirma_senha" id="confirma_senha" placeholder="Confirme sua senha" required>
                            </div>

                            <div class="password-strength" id="passwordStrength" style="display: none;">
                                <div class="strength-meter">
                                    <div class="strength-bar" id="strengthBar"></div>
                                </div>
                                <span class="strength-text" id="strengthText"></span>
                            </div>

                            <div class="recuperar-buttons">
                                <button type="submit" class="btn-primary">Redefinir Senha</button>
                                <button type="button" class="btn-secondary" onclick="voltarLogin()">Voltar</button>
                            </div>

                            <p class="form-link-center">
                                <a href="Login.php">Fazer login</a>
                            </p>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <footer></footer>

    <script>
        // Validar CPF na Etapa 1
        document.getElementById('cpf')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.length > 0) {
                // Formatar como XXX.XXX.XXX-XX
                if (value.length <= 3) {
                    value = value;
                } else if (value.length <= 6) {
                    value = value.slice(0, 3) + '.' + value.slice(3);
                } else if (value.length <= 9) {
                    value = value.slice(0, 3) + '.' + value.slice(3, 6) + '.' + value.slice(6);
                } else {
                    value = value.slice(0, 3) + '.' + value.slice(3, 6) + '.' + value.slice(6, 9) + '-' + value.slice(9, 11);
                }
            }
            
            e.target.value = value;
        });

        // Monitorar força da senha
        document.getElementById('nova_senha')?.addEventListener('input', function() {
            const senha = this.value;
            const strengthDiv = document.getElementById('passwordStrength');
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');

            if (senha.length === 0) {
                strengthDiv.style.display = 'none';
                return;
            }

            strengthDiv.style.display = 'block';

            let forca = 0;
            let texto = '';
            let cor = '';

            // Verificar comprimento
            if (senha.length >= 6) forca += 20;
            if (senha.length >= 8) forca += 20;
            if (senha.length >= 12) forca += 20;

            // Verificar tipos de caracteres
            if (/[a-z]/.test(senha)) forca += 10;
            if (/[A-Z]/.test(senha)) forca += 10;
            if (/[0-9]/.test(senha)) forca += 10;
            if (/[^a-zA-Z0-9]/.test(senha)) forca += 10;

            if (forca < 40) {
                texto = 'Fraca';
                cor = '#ff6b6b';
            } else if (forca < 70) {
                texto = 'Média';
                cor = '#ffd93d';
            } else {
                texto = 'Forte';
                cor = '#51cf66';
            }

            strengthBar.style.width = forca + '%';
            strengthBar.style.backgroundColor = cor;
            strengthText.textContent = texto;
            strengthText.style.color = cor;
        });

        // Validar confirmação de senha
        document.getElementById('confirma_senha')?.addEventListener('input', function() {
            const nova_senha = document.getElementById('nova_senha').value;
            const confirma_senha = this.value;
            
            if (confirma_senha.length > 0) {
                if (nova_senha === confirma_senha) {
                    this.style.borderColor = '#51cf66';
                } else {
                    this.style.borderColor = '#ff6b6b';
                }
            }
        });

        // Validar formulário ao enviar
        document.getElementById('verificacaoForm')?.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const cpf = document.getElementById('cpf').value.trim();
            
            if (!email || !cpf) {
                e.preventDefault();
                alert('Email e CPF são obrigatórios!');
                return false;
            }

            if (!email.includes('@')) {
                e.preventDefault();
                alert('Digite um email válido!');
                return false;
            }
        });

        document.getElementById('resetForm')?.addEventListener('submit', function(e) {
            const nova_senha = document.getElementById('nova_senha').value;
            const confirma_senha = document.getElementById('confirma_senha').value;
            
            if (nova_senha !== confirma_senha) {
                e.preventDefault();
                alert('As senhas não conferem!');
                return false;
            }

            if (nova_senha.length < 6) {
                e.preventDefault();
                alert('A senha deve ter no mínimo 6 caracteres!');
                return false;
            }
        });

        function voltarLogin() {
            window.location.href = "Login.php";
        }
    </script>
</body>
</html>
