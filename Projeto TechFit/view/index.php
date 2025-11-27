<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: Login.php');
    exit;
}

require_once __DIR__ . '/../controller/controller.php';

$usuario_nome = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Usuário';
$usuario_email = isset($_SESSION['usuario_email']) ? $_SESSION['usuario_email'] : '';
$usuario_id = $_SESSION['usuario_id'];

$controller = new CadastroController();
$resultado_pagamentos = $controller->obterPagamentos($usuario_id);
$pagamentos = $resultado_pagamentos['sucesso'] ? $resultado_pagamentos['pagamentos'] : [];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Login_Cadastro.css">
    <title>TechFit - Dashboard</title>
    <style>
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e0e0e0;
        }

        .user-info h1 {
            color: #1a1a1a;
            font-size: 28px;
            margin-bottom: 4px;
        }

        .user-info p {
            color: #666;
            font-size: 14px;
        }

        .user-actions {
            display: flex;
            gap: 12px;
        }

        .user-actions a, .user-actions button {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-pagamento {
            background-color: #3498db;
            color: white;
        }

        .btn-pagamento:hover {
            background-color: #2980b9;
        }

        .btn-logout {
            background-color: #ecf0f1;
            color: #2c3e50;
            border: 1px solid #bdc3c7;
        }

        .btn-logout:hover {
            background-color: #d5dbdb;
        }

        .dashboard-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .section {
            background-color: #ffffff;
            padding: 24px;
            border-radius: 8px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .section h2 {
            color: #1a1a1a;
            font-size: 18px;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e0e0e0;
        }

        .empty-message {
            color: #95a5a6;
            font-size: 14px;
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 6px;
        }

        .pagamento-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            background-color: #f8f9fa;
            border-radius: 6px;
            margin-bottom: 12px;
            border-left: 4px solid #3498db;
        }

        .pagamento-info h3 {
            color: #1a1a1a;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .pagamento-info p {
            color: #666;
            font-size: 12px;
        }

        .pagamento-valor {
            font-weight: 700;
            color: #27ae60;
            font-size: 14px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-confirmado {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pendente {
            background-color: #fff3cd;
            color: #856404;
        }

        @media (max-width: 600px) {
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .user-actions {
                width: 100%;
                flex-direction: column;
            }

            .user-actions a, .user-actions button {
                width: 100%;
                text-align: center;
            }

            .pagamento-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <main>
            <div class="dashboard-container">
                <!-- Header -->
                <div class="dashboard-header">
                    <div class="user-info">
                        <h1>Bem-vindo, <?php echo htmlspecialchars($usuario_nome); ?>!</h1>
                        <p><?php echo htmlspecialchars($usuario_email); ?></p>
                    </div>
                    <div class="user-actions">
                        <a href="pagamentos.php" class="btn-pagamento">🛒 Comprar Plano</a>
                        <a href="logout.php" class="btn-logout">Sair</a>
                    </div>
                </div>

                <!-- Seção: Informações do Usuário -->
                <div class="section">
                    <h2>📋 Informações do Usuário</h2>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div>
                            <p style="color: #666; font-size: 12px; margin-bottom: 4px;">ID</p>
                            <p style="color: #1a1a1a; font-weight: 600;"><?php echo $usuario_id; ?></p>
                        </div>
                        <div>
                            <p style="color: #666; font-size: 12px; margin-bottom: 4px;">Email</p>
                            <p style="color: #1a1a1a; font-weight: 600;"><?php echo htmlspecialchars($usuario_email); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Seção: Histórico de Pagamentos -->
                <div class="section">
                    <h2>💳 Histórico de Pagamentos</h2>
                    
                    <?php if (empty($pagamentos)): ?>
                        <div class="empty-message">
                            Você não tem nenhum pagamento registrado. 
                            <a href="pagamentos.php" style="color: #3498db; text-decoration: none; font-weight: 600;">Compre um plano</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pagamentos as $pagamento): ?>
                            <div class="pagamento-item">
                                <div class="pagamento-info">
                                    <h3><?php echo htmlspecialchars($pagamento['plano']); ?></h3>
                                    <p><?php echo date('d/m/Y H:i', strtotime($pagamento['data_pagamento'])); ?></p>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span class="pagamento-valor">
                                        R$ <?php echo number_format($pagamento['preco'], 2, ',', '.'); ?>
                                    </span>
                                    <span class="status-badge status-<?php echo strtolower($pagamento['status']); ?>">
                                        <?php echo ucfirst($pagamento['status']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Seção: Ações Rápidas -->
                <div class="section">
                    <h2>⚡ Ações Rápidas</h2>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <a href="recuperar_senha.php" style="padding: 12px; background-color: #e8f4ff; color: #3498db; border-radius: 6px; text-decoration: none; text-align: center; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#d0e8ff'" onmouseout="this.style.backgroundColor='#e8f4ff'">
                            🔐 Alterar Senha
                        </a>
                        <a href="logout.php" style="padding: 12px; background-color: #ffe5e5; color: #d32f2f; border-radius: 6px; text-decoration: none; text-align: center; font-weight: 600; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='#ffcccc'" onmouseout="this.style.backgroundColor='#ffe5e5'">
                            🚪 Fazer Logout
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <footer></footer>
</body>
</html>
