<?php
session_start();

// Se já está logado, redirecionar para a página principal
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../model/cadastro.php';
require_once __DIR__ . '/../model/cadastroDAO.php';

$mensagem_erro = '';
$tentativas = isset($_SESSION['tentativas_login']) ? $_SESSION['tentativas_login'] : 0;

// Bloquear após 5 tentativas
if ($tentativas >= 5) {
    $mensagem_erro = 'Muitas tentativas de login. Tente novamente mais tarde.';
}

// Verificar se o formulário foi submetido via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tentativas < 5) {
    $email_or_cpf = isset($_POST['email_or_cpf']) ? trim($_POST['email_or_cpf']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validar campos obrigatórios
    if (empty($email_or_cpf) || empty($password)) {
        $mensagem_erro = 'Email/CPF e senha são obrigatórios.';
    } else {
        try {
            $dao = new cadastroDAO();
            $cadastros = $dao->lerCadastro();
            
            $usuario_encontrado = null;
            
            // Procurar por email ou CPF
            foreach ($cadastros as $cadastro) {
                if ($cadastro->getEmail() === $email_or_cpf || $cadastro->getCpf() === $email_or_cpf) {
                    $usuario_encontrado = $cadastro;
                    break;
                }
            }
            
            // Verificar se o usuário foi encontrado e a senha está correta
            if ($usuario_encontrado && password_verify($password, $usuario_encontrado->getSenha())) {
                // Login bem-sucedido
                $_SESSION['usuario_id'] = $usuario_encontrado->getId();
                $_SESSION['usuario_nome'] = $usuario_encontrado->getNome();
                $_SESSION['usuario_email'] = $usuario_encontrado->getEmail();
                $_SESSION['tentativas_login'] = 0; // Resetar tentativas
                
                // Redirecionar para página principal
                header('Location: index.php');
                exit;
            } else {
                // Credenciais inválidas
                $mensagem_erro = 'Email/CPF ou senha incorretos.';
                $_SESSION['tentativas_login'] = $tentativas + 1;
            }
        } catch (Exception $e) {
            $mensagem_erro = 'Erro ao processar login: ' . $e->getMessage();
        }
    }
}
?>

<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Login_Cadastro.css">
    <title>TechFit - Login</title>

<body>
    <div class="container">
        <main>
            <div class="login">
                <!-- Exibir mensagem de erro se houver -->
                <?php if (!empty($mensagem_erro)): ?>
                    <div style="background-color: #ff6b6b; color: white; padding: 12px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-size: 14px;">
                        <?php echo htmlspecialchars($mensagem_erro); ?>
                    </div>
                <?php endif; ?>

                <!-- Form para autenticação: action aponta para este mesmo arquivo -->
                <form action="Login.php" method="POST" novalidate>
                    <section>
                        <img src="imagens/logo2.png" alt="Logo">
                    </section>
                    <section class="login-input">
                        <label for="email">Email ou CPF:</label>
                        <input type="text" name="email_or_cpf" id="email" placeholder="Digite seu email ou CPF" required>
                        <label for="senha">Senha:</label>
                        <input type="password" name="password" id="senha" placeholder="Digite sua senha" required>
                        <a href="">Esqueceu sua senha?</a>
                    </section>
                    <section class="redirect">
                        <!-- submit envia o form ao servidor -->
                        <button type="submit" class="primary">Entrar</button>
                        <!-- botão secundário navega para página de cadastro sem submeter -->
                        <button type="button" class="secondary" onclick="cadastro()">Criar conta</button>
                    </section>
                </form>
            </div>
        </main>
    </div>

    <footer>

    </footer>

    <script>
        function cadastro() {
            window.location.href = "Cadastro.php";
        }
    </script>
</body>

</html>