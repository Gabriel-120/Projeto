<?php
session_start();

// Se já está logado, redirecionar para a página principal
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../controller/controller.php';

$mensagem_erro = '';
$mensagem_sucesso = '';
$tentativas = isset($_SESSION['tentativas_login']) ? $_SESSION['tentativas_login'] : 0;

// Bloquear após 5 tentativas
if ($tentativas >= 5) {
    $mensagem_erro = 'Muitas tentativas de login. Tente novamente mais tarde.';
}

// Criar instância do controller
$controller = new CadastroController();

// Verificar se o formulário foi submetido via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tentativas < 5) {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validar campos obrigatórios
    if (empty($email) || empty($password)) {
        $mensagem_erro = 'Email e senha são obrigatórios.';
    } else {
        // Usar o controller para autenticar
        $resultado = $controller->autenticar($email, $password);

        if ($resultado['sucesso']) {
            $usuario = $resultado['usuario'];
            
            // Login bem-sucedido
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['tentativas_login'] = 0; // Resetar tentativas
            
            // Exibir mensagem de sucesso por 2 segundos e redirecionar
            $mensagem_sucesso = 'Bem-vindo, ' . htmlspecialchars($usuario['nome']) . '!';
            header('Refresh: 2; url=index.php');
        } else {
            // Credenciais inválidas
            $mensagem_erro = $resultado['erro'];
            $_SESSION['tentativas_login'] = $tentativas + 1;
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
    <title>TechFit - Login</title>
</head>
<body>
    <div class="container">
        <main>
            <div class="login">
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
                    <!-- Formulário de Login -->
                    <form action="Login.php" method="POST" id="loginForm" novalidate>
                        <div class="login-container">
                            <h2>Faça seu Login</h2>

                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" name="email" id="email" placeholder="Digite seu email" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="password">Senha *</label>
                                <input type="password" name="password" id="password" placeholder="Digite sua senha" required>
                            </div>

                            <p class="form-link-center">
                                <a href="recuperar_senha.php" class="forgot-password">Esqueci minha senha</a>
                            </p>

                            <!-- Botões -->
                            <div class="login-buttons">
                                <button type="submit" class="btn-primary">Entrar</button>
                            </div>

                            <p class="form-link-center">
                                Não tem conta? <a href="Cadastro.php">Cadastre-se aqui</a>
                            </p>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <footer></footer>

    <script>
        // Validar formulário ao enviar
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Email e senha são obrigatórios!');
                return false;
            }

            if (!email.includes('@')) {
                e.preventDefault();
                alert('Digite um email válido!');
                return false;
            }
        });
    </script>
</body>
</html>