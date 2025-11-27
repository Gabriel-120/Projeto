<?php
/**
 * setup.php - Script de inicialização do TechFit
 * 
 * Crie um arquivo chamado "setup.php" na raiz do projeto
 * e execute em seu navegador para configurar o banco de dados
 * 
 * URL: http://localhost:8000/setup.php
 */

require_once 'Projeto TechFit/model/connection.php';

// Configurações de segurança
header('Content-Type: text/html; charset=utf-8');
$_SESSION = [];

// Verificar se é uma solicitação POST para configurar
$setup_completo = false;
$mensagem_erro = '';
$mensagem_sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar token de segurança básico
    if (!isset($_POST['setup_token']) || $_POST['setup_token'] !== 'techfit_setup') {
        $mensagem_erro = 'Token de segurança inválido!';
    } else {
        try {
            $connection = Connection::getInstance();
            $connection->criarTabelas();
            $setup_completo = true;
            $mensagem_sucesso = 'Banco de dados configurado com sucesso! Você pode agora acessar a aplicação.';
        } catch (Exception $e) {
            $mensagem_erro = 'Erro ao configurar banco: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFit - Setup</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .setup-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }

        .setup-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .setup-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .setup-header p {
            color: #666;
            font-size: 14px;
        }

        .logo {
            width: 80px;
            height: auto;
            margin: 0 auto 20px;
            display: block;
        }

        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }

        .alert.show {
            display: block;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .setup-content {
            margin-bottom: 30px;
        }

        .setup-steps {
            list-style: none;
            counter-reset: step-counter;
        }

        .setup-steps li {
            counter-increment: step-counter;
            margin-bottom: 16px;
            padding-left: 40px;
            position: relative;
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }

        .setup-steps li:before {
            content: counter(step-counter);
            position: absolute;
            left: 0;
            top: 0;
            background: #667eea;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 13px;
        }

        .setup-steps li.done:before {
            background: #27ae60;
            content: "✓";
            font-size: 18px;
        }

        .setup-buttons {
            display: flex;
            gap: 12px;
        }

        .btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background-color: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background-color: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background-color: #ecf0f1;
            color: #333;
        }

        .btn-secondary:hover {
            background-color: #d5dbdb;
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .setup-info {
            background-color: #f8f9fa;
            padding: 16px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
            margin-top: 20px;
            font-size: 13px;
            color: #666;
            line-height: 1.6;
        }

        .setup-info strong {
            color: #333;
        }

        .success-icon {
            text-align: center;
            font-size: 48px;
            margin: 20px 0;
        }

        @media (max-width: 480px) {
            .setup-container {
                padding: 20px;
            }

            .setup-header h1 {
                font-size: 22px;
            }

            .setup-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <!-- Header -->
        <div class="setup-header">
            <h1>🚀 TechFit Setup</h1>
            <p>Configurar banco de dados</p>
        </div>

        <!-- Mensagens -->
        <?php if (!empty($mensagem_sucesso)): ?>
            <div class="alert alert-success show">
                ✓ <?php echo $mensagem_sucesso; ?>
                <div class="success-icon">✓</div>
            </div>
        <?php elseif (!empty($mensagem_erro)): ?>
            <div class="alert alert-error show">
                ✗ <?php echo $mensagem_erro; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info show">
                ℹ️ Clique em "Iniciar Setup" para criar as tabelas do banco de dados
            </div>
        <?php endif; ?>

        <!-- Conteúdo -->
        <?php if ($setup_completo): ?>
            <!-- Sucesso -->
            <div class="setup-content">
                <div class="setup-info">
                    <strong>Próximas etapas:</strong>
                    <ol style="margin-top: 8px; padding-left: 20px;">
                        <li>Faça login em <a href="Projeto TechFit/view/Login.php" style="color: #667eea;">Login.php</a></li>
                        <li>Crie sua primeira conta em <a href="Projeto TechFit/view/Cadastro.php" style="color: #667eea;">Cadastro.php</a></li>
                        <li>Acesse seus pagamentos em <a href="Projeto TechFit/view/pagamentos.php" style="color: #667eea;">pagamentos.php</a></li>
                    </ol>
                </div>

                <div class="setup-buttons" style="margin-top: 30px;">
                    <a href="Projeto TechFit/view/Login.php" class="btn btn-primary">
                        ➜ Ir para Login
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Instruções -->
            <div class="setup-content">
                <h3 style="color: #333; font-size: 16px; margin-bottom: 16px;">Configuração do Banco de Dados</h3>
                
                <ol class="setup-steps">
                    <li>Certifique-se de que o MySQL está rodando</li>
                    <li>Crie um banco chamado <strong>techfit</strong></li>
                    <li>Configure as credenciais em <strong>model/connection.php</strong></li>
                    <li>Clique no botão abaixo para criar as tabelas</li>
                </ol>

                <div class="setup-info">
                    <strong>Credenciais padrão:</strong><br>
                    Host: localhost<br>
                    Usuário: root<br>
                    Senha: (sem senha)<br>
                    Banco: techfit
                </div>

                <!-- Formulário -->
                <form method="POST" style="margin-top: 20px;">
                    <input type="hidden" name="setup_token" value="techfit_setup">
                    <div class="setup-buttons">
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Tem certeza? Isso criará as tabelas do banco.');">
                            ⚙️ Iniciar Setup
                        </button>
                        <a href="Projeto TechFit/view/Login.php" class="btn btn-secondary">
                            ➜ Cancelar
                        </a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
