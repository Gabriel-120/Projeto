<?php
require_once __DIR__ . '/../app/helpers/loadModels.php';

// Garantir sessão ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensagem_erro = '';
$mensagem_sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $cpf = isset($_POST['cpf']) ? trim($_POST['cpf']) : '';
    $data_nascimento = isset($_POST['nascimento']) ? trim($_POST['nascimento']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    if ($password !== $confirm_password) {
        $mensagem_erro = 'As senhas não conferem';
    } else {
        $res = CadastroModel::register($nome, $email, $cpf, $data_nascimento, $password);
        if ($res['sucesso']) {
            $mensagem_sucesso = $res['mensagem'];
            header('Refresh: 2; url=login.php');
        } else {
            $mensagem_erro = $res['erro'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/Login_Cadastro.css">
    <title>TechFit - Cadastro</title>
</head>
<body>
    <div class="login-page">
        <div class="container">
            <div class="main-card">
            <section class="logo-section">
                <img src="/assets/images/logo.png" alt="Logo TechFit" class="logo">
            </section>

            <section class="form-section">
                <?php if (!empty($mensagem_erro)): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($mensagem_erro); ?></div>
                <?php endif; ?>

                <?php if (!empty($mensagem_sucesso)): ?>
                    <div class="alert alert-success">✓ <?php echo htmlspecialchars($mensagem_sucesso); ?></div>
                <?php else: ?>
                    <form action="cadastro.php" method="POST" id="registerForm">
                        <div class="cadastro-container">
                            <div class="cadastro-left">
                                <h2>Dados Pessoais</h2>
                                <div class="form-group">
                                    <label for="nome">Nome Completo *</label>
                                    <input type="text" name="nome" id="nome" placeholder="Digite seu nome completo" value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email *</label>
                                    <input type="email" name="email" id="email" placeholder="exemplo@gmail.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="cpf">CPF *</label>
                                    <input type="text" name="cpf" id="cpf" placeholder="000.000.000-00" value="<?php echo isset($_POST['cpf']) ? htmlspecialchars($_POST['cpf']) : ''; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="nascimento">Data de Nascimento *</label>
                                    <input type="date" name="nascimento" id="nascimento" value="<?php echo isset($_POST['nascimento']) ? htmlspecialchars($_POST['nascimento']) : ''; ?>" required>
                                </div>
                            </div>

                            <div class="cadastro-right">
                                <h2>Segurança</h2>
                                <div class="form-group">
                                    <label for="password">Senha *</label>
                                    <input type="password" name="password" id="password" placeholder="Digite sua senha" required>
                                    <small class="form-hint">Mínimo 6 caracteres</small>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Confirmar Senha *</label>
                                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirme sua senha" required>
                                </div>
                                <div class="password-strength" id="passwordStrength" style="display: none;">
                                    <div class="strength-meter"><div class="strength-bar" id="strengthBar"></div></div>
                                    <span class="strength-text" id="strengthText"></span>
                                </div>
                            </div>
                        </div>
                        <div class="cadastro-buttons">
                            <button type="submit" class="btn-primary">Cadastrar</button>
                        </div>
                        <p class="form-link">Já tem conta? <a href="login.php">Faça login aqui</a></p>
                    </form>
                <?php endif; ?>
            </section>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('password').addEventListener('input', function() {
            const senha = this.value;
            const strengthDiv = document.getElementById('passwordStrength');
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');

            if (senha.length === 0) { strengthDiv.style.display = 'none'; return; }
            strengthDiv.style.display = 'block';
            let forca = 0;
            if (senha.length >= 6) forca += 20;
            if (senha.length >= 8) forca += 20;
            if (senha.length >= 12) forca += 20;
            if (/[a-z]/.test(senha)) forca += 10;
            if (/[A-Z]/.test(senha)) forca += 10;
            if (/[0-9]/.test(senha)) forca += 10;
            if (/[^a-zA-Z0-9]/.test(senha)) forca += 10;
            let texto = '';
            let cor = '';
            if (forca < 40) { texto = 'Fraca'; cor = '#ff6b6b'; }
            else if (forca < 70) { texto = 'Média'; cor = '#ffd93d'; }
            else { texto = 'Forte'; cor = '#51cf66'; }
            strengthBar.style.width = forca + '%'; strengthBar.style.backgroundColor = cor; strengthText.textContent = texto; strengthText.style.color = cor;
        });

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            if (password !== confirm) { e.preventDefault(); alert('As senhas não conferem!'); return false; }
            if (password.length < 6) { e.preventDefault(); alert('A senha deve ter no mínimo 6 caracteres.'); return false; }
        });

        // CPF mask
        const cpfInput = document.getElementById('cpf');
        cpfInput.addEventListener('input', function(e){
            let v = this.value.replace(/\D/g,'');
            if (v.length > 11) v = v.slice(0,11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            this.value = v;
        });

        function validateCPF(cpf){
            cpf = cpf.replace(/\D/g,'');
            if (cpf.length !== 11) return false;
            // simples verificação de repetição
            if (/^(\d)\1+$/.test(cpf)) return false;
            // cálculo básico
            let sum = 0, rest;
            for (let i=1;i<=9;i++) sum = sum + parseInt(cpf.substring(i-1,i)) * (11 - i);
            rest = (sum * 10) % 11;
            if ((rest === 10) || (rest === 11)) rest = 0;
            if (rest !== parseInt(cpf.substring(9,10))) return false;
            sum = 0;
            for (let i=1;i<=10;i++) sum = sum + parseInt(cpf.substring(i-1,i)) * (12 - i);
            rest = (sum * 10) % 11;
            if ((rest === 10) || (rest === 11)) rest = 0;
            if (rest !== parseInt(cpf.substring(10,11))) return false;
            return true;
        }

        document.getElementById('registerForm').addEventListener('submit', function(e){
            const cpf = document.getElementById('cpf').value;
            if (!validateCPF(cpf)) { e.preventDefault(); alert('CPF inválido'); return false; }
        });
    </script>
</body>
</html>
