<?php
require_once __DIR__ . '/../app/helpers/loadModels.php';

$mensagem_erro = '';

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
            // carregar mais dados será feito por index.php
            header('Location: index.php');
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
    <link rel="stylesheet" href="../Gabriel/view/Login_Cadastro.css">
    <title>TechFit - Login</title>
</head>
<body>
    <div class="container">
        <main>
            <div class="login">
                <section class="logo-section">
                    <img src="../Gabriel/view/imagens/logo2.png" alt="Logo TechFit" class="logo">
                </section>

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

                    <p class="form-link-center">Não tem conta? <a href="cadastro.php">Cadastre-se aqui</a></p>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
