<?php
require_once __DIR__ . '/../app/helpers/loadModels.php';

// garantir sessão ativa
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// verificar autenticação
$uid = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : (isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0);
if ($uid <= 0) {
    $return = urlencode($_SERVER['REQUEST_URI']);
    header('Location: login.php?return_to=' . $return);
    exit;
}

require_once __DIR__ . '/../app/models/Planos.php';
require_once __DIR__ . '/../app/models/Connect.php';

$mensagem_erro = '';
$mensagem_sucesso = '';

$planos = Planos::getAll();
$planosById = [];
foreach ($planos as $p) $planosById[(int)$p['id_plano']] = $p;

$selectedId = isset($_GET['id_plano']) ? intval($_GET['id_plano']) : 0;
if ($selectedId === 0 && isset($_GET['plano'])) {
    $name = urldecode($_GET['plano']);
    foreach ($planos as $p) {
        if ($p['nome_plano'] === $name) { $selectedId = (int)$p['id_plano']; break; }
    }
}

$selectedPreco = isset($_GET['preco']) ? floatval($_GET['preco']) : ($selectedId ? floatval($planosById[$selectedId]['preco'] ?? 0) : 0.0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = isset($_POST['plano']) ? intval($_POST['plano']) : 0;
    $nome_cartao = trim($_POST['nome_cartao'] ?? '');
    $cpf = trim($_POST['cpf_pagamento'] ?? '');
    $numero_cartao = trim($_POST['numero_cartao'] ?? '');
    $cvv = trim($_POST['cvv'] ?? '');

    if ($postId <= 0 || !isset($planosById[$postId])) {
        $mensagem_erro = 'Selecione um plano válido.';
    } elseif ($nome_cartao === '' || $cpf === '' || $numero_cartao === '' || $cvv === '') {
        $mensagem_erro = 'Todos os campos são obrigatórios.';
    } else {
        $numeros_cartao = preg_replace('/\D/', '', $numero_cartao);
        if (strlen($numeros_cartao) < 13) {
            $mensagem_erro = 'Número de cartão inválido.';
        } elseif (strlen($cvv) < 3) {
            $mensagem_erro = 'CVV inválido.';
        } else {
            try {
                $pdo = Connect::conectar();
                
                // Obter o ID do aluno a partir do usuário
                $stmt = $pdo->prepare('SELECT id_aluno FROM Alunos WHERE id_usuario = ?');
                $stmt->execute([$uid]);
                $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$aluno) {
                    $mensagem_erro = 'Perfil de aluno não encontrado.';
                } else {
                    $idAluno = $aluno['id_aluno'];
                    
                    // Criar registro de pagamento
                    $planoNome = $planosById[$postId]['nome_plano'];
                    $planoPreco = (float)$planosById[$postId]['preco'];
                    
                    $stmtPag = $pdo->prepare('INSERT INTO Pagamentos (status, data_pagamento, valor, metodo_pagamento, id_aluno) VALUES (?, NOW(), ?, ?, ?)');
                    $stmtPag->execute(['concluido', $planoPreco, 'cartao', $idAluno]);
                    
                    // Vincular plano ao aluno na tabela Planos_Aluno
                    $duracao = intval($planosById[$postId]['duracao'] ?? 30);
                    $dataInicio = date('Y-m-d');
                    $dataFim = date('Y-m-d', strtotime("+{$duracao} days"));
                    
                    $stmtPlan = $pdo->prepare('INSERT INTO Planos_Aluno (id_aluno, id_plano, data_inicio, data_fim, status) VALUES (?, ?, ?, ?, ?)');
                    $stmtPlan->execute([$idAluno, $postId, $dataInicio, $dataFim, 'ativo']);
                    
                    $mensagem_sucesso = 'Pagamento realizado com sucesso! Seu plano foi ativado.';
                }
            } catch (Exception $e) {
                $mensagem_erro = 'Erro ao processar pagamento: ' . $e->getMessage();
            }
        }
    }
}

// Encontrar nome do usuário se disponível na sessão
$usuario_nome = $_SESSION['user_name'] ?? ($_SESSION['usuario_nome'] ?? 'Usuário');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFit - Planos e Pagamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .payment-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 900px;
            width: 100%;
            overflow: hidden;
        }

        .payment-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }

        .payment-header h2 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .header-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .payment-body {
            padding: 40px;
        }

        .plan-selection {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 25px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .plan-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .plan-option {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .plan-option:hover {
            border-color: #667eea;
            background: #f0f4ff;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .plan-option input[type="radio"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #667eea;
        }

        .plan-content {
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .plan-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: #333;
        }

        .plan-price {
            font-size: 1.3rem;
            color: #667eea;
            font-weight: bold;
            margin: 5px 0;
        }

        .plan-description {
            font-size: 0.9rem;
            color: #666;
        }

        .secure-badge {
            text-align: center;
            padding: 15px;
            background: #e8f5e9;
            border-radius: 8px;
            color: #2e7d32;
            font-weight: 600;
            margin-top: 20px;
        }

        .payment-details {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
            margin-top: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .card-icons {
            display: flex;
            gap: 15px;
            margin: 20px 0;
            flex-wrap: wrap;
        }

        .card-icon {
            padding: 10px 15px;
            background: #f0f0f0;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #666;
        }

        .payment-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-primary,
        .btn-secondary {
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        .btn-large {
            padding: 15px 30px !important;
            min-height: 50px;
        }

        .payment-info {
            font-size: 0.85rem;
            color: #666;
            margin-top: 20px;
            text-align: center;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ef5350;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #66bb6a;
        }

        @media (max-width: 600px) {
            .payment-header {
                padding: 20px;
            }

            .payment-body {
                padding: 20px;
            }

            .payment-header h2 {
                font-size: 1.5rem;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .payment-buttons {
                grid-template-columns: 1fr;
            }

            .plan-options {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <header class="payment-header">
            <h2>Escolha seu Plano</h2>
            <p class="header-subtitle">Bem-vindo, <?php echo htmlspecialchars($usuario_nome); ?>!</p>
        </header>

        <div class="payment-body">
            <?php if (!empty($mensagem_erro)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($mensagem_erro); ?></div>
            <?php endif; ?>

            <?php if (!empty($mensagem_sucesso)): ?>
                <div class="alert alert-success">✓ <?php echo htmlspecialchars($mensagem_sucesso); ?></div>
                <div style="text-align: center; margin-top: 30px;">
                    <button onclick="voltarDashboard()" class="btn btn-primary btn-large">Voltar ao Dashboard</button>
                </div>
            <?php else: ?>

                <form method="POST" action="pagamentos.php" id="paymentForm">

                    <section class="plan-selection">
                        <h3 class="section-title">Selecione seu plano</h3>
                        <div class="plan-options">
                            <?php foreach ($planos as $p):
                                $id = (int)$p['id_plano'];
                                $checked = ($selectedId && $selectedId === $id) ? 'checked' : '';
                            ?>
                            <label class="plan-option">
                                <input type="radio" name="plano" value="<?php echo $id; ?>" <?php echo $checked; ?> required>
                                <div class="plan-content">
                                    <span class="plan-name"><?php echo htmlspecialchars($p['nome_plano']); ?></span>
                                    <span class="plan-price">R$ <?php echo number_format($p['preco'], 2, ',', '.'); ?>/mês</span>
                                    <span class="plan-description"><?php echo htmlspecialchars($p['descricao_plano'] ?? ''); ?></span>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <div class="secure-badge">🔒 Pagamento Seguro com Criptografia</div>
                    </section>

                    <section class="payment-details">
                        <h3 class="section-title">Detalhes do Pagamento</h3>
                        <div class="form-group">
                            <label for="nome_cartao">Nome no Cartão *</label>
                            <input type="text" id="nome_cartao" name="nome_cartao" placeholder="Como aparece no cartão" required>
                        </div>
                        <div class="form-group">
                            <label for="cpf_pagamento">CPF *</label>
                            <input type="text" id="cpf_pagamento" name="cpf_pagamento" placeholder="000.000.000-00" required>
                        </div>
                        <div class="form-group">
                            <label for="numero_cartao">Número do Cartão *</label>
                            <input type="text" id="numero_cartao" name="numero_cartao" placeholder="0000 0000 0000 0000" maxlength="19" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="data_expiracao">Data de Validade *</label>
                                <input type="text" id="data_expiracao" name="data_expiracao" placeholder="MM/YY" maxlength="5" required>
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV *</label>
                                <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="4" required>
                            </div>
                        </div>

                        <div class="card-icons">
                            <span class="card-icon">💳 VISA</span>
                            <span class="card-icon">💳 MASTERCARD</span>
                            <span class="card-icon">💳 ELO</span>
                            <span class="card-icon">💳 HIPERCARD</span>
                        </div>

                        <div class="payment-buttons">
                            <button type="submit" class="btn-primary btn-large">Finalizar Pagamento</button>
                            <button type="button" class="btn-secondary btn-large" onclick="voltarDashboard()">Cancelar</button>
                        </div>

                        <p class="payment-info">Ao clicar em "Finalizar Pagamento", você concorda com nossos termos de serviço. Seu pagamento será processado de forma segura.</p>
                    </section>

                </form>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Formatador número de cartão
        const numEl = document.getElementById('numero_cartao');
        if (numEl) numEl.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formatted = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formatted;
        });

        // Formatador data de expiração
        const dataEl = document.getElementById('data_expiracao');
        if (dataEl) dataEl.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 2) value = value.slice(0,2) + '/' + value.slice(2,4);
            e.target.value = value;
        });

        // Formatador CPF
        const cpfEl = document.getElementById('cpf_pagamento');
        if (cpfEl) cpfEl.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                if (value.length <= 3) value = value;
                else if (value.length <= 6) value = value.slice(0,3) + '.' + value.slice(3);
                else if (value.length <= 9) value = value.slice(0,3) + '.' + value.slice(3,6) + '.' + value.slice(6);
                else value = value.slice(0,3) + '.' + value.slice(3,6) + '.' + value.slice(6,9) + '-' + value.slice(9,11);
            }
            e.target.value = value;
        });

        // Validador CVV (apenas números)
        const cvvEl = document.getElementById('cvv');
        if (cvvEl) cvvEl.addEventListener('input', function(e) { 
            e.target.value = e.target.value.replace(/\D/g, ''); 
        });

        // Validação do formulário
        const form = document.getElementById('paymentForm');
        if (form) form.addEventListener('submit', function(e) {
            const plano = document.querySelector('input[name="plano"]:checked');
            const nome = document.getElementById('nome_cartao')?.value.trim();
            const cpf = document.getElementById('cpf_pagamento')?.value.trim();
            const numero = document.getElementById('numero_cartao')?.value.trim();
            const data = document.getElementById('data_expiracao')?.value.trim();
            const cvv = document.getElementById('cvv')?.value.trim();
            
            if (!plano) { 
                e.preventDefault(); 
                alert('Selecione um plano para continuar!'); 
                return false; 
            }
            if (!nome || !cpf || !numero || !data || !cvv) { 
                e.preventDefault(); 
                alert('Preencha todos os campos obrigatórios!'); 
                return false; 
            }
            if (!data.includes('/') || data.split('/')[0].length !== 2) { 
                e.preventDefault(); 
                alert('Data de validade inválida! Use o formato MM/YY'); 
                return false; 
            }
            const numeros_cartao = numero.replace(/\s/g, '');
            if (numeros_cartao.length < 13) { 
                e.preventDefault(); 
                alert('Número de cartão inválido!'); 
                return false; 
            }
            if (cvv.length < 3) { 
                e.preventDefault(); 
                alert('CVV inválido!'); 
                return false; 
            }
        });

        function voltarDashboard() { 
            window.location.href = '/'; 
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
