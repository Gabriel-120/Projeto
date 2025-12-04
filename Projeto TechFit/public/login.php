<?php
require_once __DIR__ . '/../app/helpers/loadModels.php';

// Garantir que a sessão esteja ativa antes de usar $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$mensagem_erro = '';

// permitir retorno para URL segura após login
$return_to = '';
if (!empty($_GET['return_to'])) {
    $return_to = $_GET['return_to'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        $mensagem_erro = 'Email e senha são obrigatórios.';
    } else {
        $pdo = Connect::conectar();
        $sql = "SELECT id_usuario, nome, email, senha_hash, tipo FROM Usuarios WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['senha_hash'])) {
            // login ok - usar chave de sessão compatível com index.php
            $_SESSION['user_id'] = $user['id_usuario'];
            // redirecionar para return_to se fornecido (somente caminhos relativos simples)
            $postReturn = '';
            if (!empty($_POST['return_to'])) $postReturn = $_POST['return_to'];
            $target = '/index.php';
            if ($postReturn) {
                // simples validação: só aceitar caminhos que começam com '/' para evitar redirecionamento externo
                if (strpos($postReturn, '/') === 0) $target = $postReturn;
            } elseif ($return_to) {
                if (strpos($return_to, '/') === 0) $target = $return_to;
            }

            header('Location: ' . $target);
            exit;
        } else {
            $mensagem_erro = 'Email ou senha incorretos.';
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
    <title>TechFit - Login</title>
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

                <form action="login.php" method="POST" class="login-container">
                    <h2>Faça seu Login</h2>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" name="email" id="email" placeholder="Digite seu email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Senha *</label>
                        <input type="password" name="password" id="password" placeholder="Digite sua senha" required>
                    </div>

                    <p class="form-link-center"><a href="recuperar_senha.php" class="forgot-password">Esqueci minha senha</a></p>

                    <div class="login-buttons">
                        <button type="submit" class="btn-primary">Entrar</button>
                    </div>

                    <?php if (!empty($return_to)): ?>
                        <input type="hidden" name="return_to" value="<?php echo htmlspecialchars($return_to); ?>">
                    <?php endif; ?>

                    <p class="form-link-center">Não tem conta? <a href="cadastro.php">Cadastre-se aqui</a></p>
                </form>
            </section>
        </div>
    </div>
</body>
</html>
