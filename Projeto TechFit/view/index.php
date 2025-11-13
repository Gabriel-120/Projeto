<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: Login.php');
    exit;
}

$usuario_nome = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Usuário';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFit - Home</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .header {
            width: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #00d1ff;
            font-size: 24px;
        }

        .header-user {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .header-user p {
            color: #fff;
            font-size: 16px;
        }

        .logout-btn {
            background-color: #ff6b6b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #ff5252;
        }

        .container {
            margin-top: 100px;
            background-color: rgba(30, 30, 30, 0.95);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.45);
            max-width: 800px;
            width: 100%;
            color: #fff;
            text-align: center;
        }

        .container h2 {
            color: #00d1ff;
            font-size: 32px;
            margin-bottom: 20px;
        }

        .container p {
            font-size: 16px;
            line-height: 1.6;
            color: #e0e0e0;
            margin-bottom: 30px;
        }

        .welcome-message {
            background-color: rgba(0, 209, 255, 0.1);
            border-left: 4px solid #00d1ff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .welcome-message h3 {
            color: #00d1ff;
            margin-bottom: 10px;
        }

        .features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 40px;
        }

        .feature {
            background-color: rgba(0, 209, 255, 0.1);
            padding: 20px;
            border-radius: 8px;
            border: 1px solid rgba(0, 209, 255, 0.3);
            transition: all 0.3s ease;
        }

        .feature:hover {
            background-color: rgba(0, 209, 255, 0.2);
            border-color: #00d1ff;
        }

        .feature h4 {
            color: #00d1ff;
            margin-bottom: 10px;
        }

        .feature p {
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .header {
                flex-direction: column;
                gap: 15px;
            }

            .header-user {
                width: 100%;
                justify-content: space-between;
            }

            .container {
                padding: 20px;
                margin-top: 140px;
            }

            .features {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>🏋️ TechFit</h1>
        <div class="header-user">
            <p>Bem-vindo, <strong><?php echo htmlspecialchars($usuario_nome); ?></strong>!</p>
            <form action="logout.php" method="POST" style="margin: 0;">
                <button type="submit" class="logout-btn">Sair</button>
            </form>
        </div>
    </div>

    <!-- Conteúdo Principal -->
    <div class="container">
        <h2>Bem-vindo à TechFit!</h2>
        
        <div class="welcome-message">
            <h3>✓ Você está logado com sucesso!</h3>
            <p>Seu cadastro foi confirmado e você agora tem acesso a todos os nossos serviços.</p>
        </div>

        <p>Explore nossos recursos e comece seu treino agora mesmo!</p>

        <!-- Features (placeholder para funcionalidades futuras) -->
        <div class="features">
            <div class="feature">
                <h4>📋 Planos de Treino</h4>
                <p>Acesse planos personalizados de acordo com seu nível de experiência.</p>
            </div>
            <div class="feature">
                <h4>👨‍🏫 Aulas Exclusivas</h4>
                <p>Assista a aulas ao vivo e sob demanda com nossos instrutores.</p>
            </div>
            <div class="feature">
                <h4>📊 Progresso</h4>
                <p>Acompanhe seu progresso e conquistas ao longo do tempo.</p>
            </div>
            <div class="feature">
                <h4>💪 Comunidade</h4>
                <p>Conecte-se com outros usuários e compartilhe suas experiências.</p>
            </div>
        </div>
    </div>
</body>
</html>
