<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: Login.php');
    exit;
}

require_once __DIR__ . '/../controller/controller.php';

$mensagem_erro = '';
$mensagem_sucesso = '';
$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : 'Usuário';

// Planos disponíveis
$planos = [
    'basico' => ['nome' => 'Plano Básico', 'preco' => 29.90, 'descricao' => 'Acesso básico à plataforma'],
    'profissional' => ['nome' => 'Plano Profissional', 'preco' => 79.90, 'descricao' => 'Acesso completo com recursos avançados'],
    'premium' => ['nome' => 'Plano Premium', 'preco' => 149.90, 'descricao' => 'Acesso VIP com suporte prioritário'],
];

// Processar pagamento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plano = isset($_POST['plano']) ? trim($_POST['plano']) : '';
    $nome_cartao = isset($_POST['nome_cartao']) ? trim($_POST['nome_cartao']) : '';
    $cpf = isset($_POST['cpf_pagamento']) ? trim($_POST['cpf_pagamento']) : '';
    $numero_cartao = isset($_POST['numero_cartao']) ? trim($_POST['numero_cartao']) : '';
    $cvv = isset($_POST['cvv']) ? trim($_POST['cvv']) : '';

    // Validações básicas
    if (empty($plano) || !isset($planos[$plano])) {
        $mensagem_erro = 'Selecione um plano válido.';
    } elseif (empty($nome_cartao) || empty($cpf) || empty($numero_cartao) || empty($cvv)) {
        $mensagem_erro = 'Todos os campos são obrigatórios.';
    } else {
        // Validar número de cartão (mínimo 13 dígitos)
        $numeros_cartao = preg_replace('/\D/', '', $numero_cartao);
        if (strlen($numeros_cartao) < 13) {
            $mensagem_erro = 'Número de cartão inválido.';
        } elseif (strlen($cvv) < 3) {
            $mensagem_erro = 'CVV inválido.';
        } else {
            // Registrar pagamento no banco de dados
            $controller = new CadastroController();
            $resultado = $controller->registrarPagamento(
                $usuario_id,
                $planos[$plano]['nome'],
                $planos[$plano]['preco']
            );

            if ($resultado['sucesso']) {
                $mensagem_sucesso = 'Pagamento realizado com sucesso! Seu plano foi ativado.';
                // Redirecionar após 3 segundos
                header('Refresh: 3; url=index.php');
            } else {
                $mensagem_erro = $resultado['erro'];
            }
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
    <link rel="stylesheet" href="pagamento.css">
    <title>TechFit - Planos e Pagamento</title>
</head>
<body>
    <div class="container">
        <main>
            <div class="payment-container">
                
                <!-- Header com Logo -->
                <header class="payment-header">
                    <div class="logo">
                        <img src="imagens/logo2.png" alt="Logo TechFit">
                    </div>
                    <h2>Escolha seu Plano</h2>
                    <p class="header-subtitle">Bem-vindo, <?php echo htmlspecialchars($usuario_nome); ?>!</p>
                </header>

                <!-- Exibir mensagens -->
                <?php if (!empty($mensagem_erro)): ?>
                    <div class="alert alert-error">
                        <?php echo htmlspecialchars($mensagem_erro); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($mensagem_sucesso)): ?>
                    <div class="alert alert-success">
                        ✓ <?php echo htmlspecialchars($mensagem_sucesso); ?>
                    </div>
                <?php else: ?>
                    <!-- Formulário de Pagamento -->
                    <form class="payment-body" method="POST" action="pagamentos.php" id="paymentForm">

                        <!-- Seção: Selecionar Plano -->
                        <section class="plan-selection">
                            <h3 class="section-title">Selecione seu plano</h3>
                            
                            <div class="plan-options">
                                <?php foreach ($planos as $key => $plano): ?>
                                    <label class="plan-option" for="plano_<?php echo $key; ?>">
                                        <input type="radio" id="plano_<?php echo $key; ?>" name="plano" 
                                               value="<?php echo $key; ?>" required>
                                        <div class="plan-content">
                                            <span class="plan-name"><?php echo $plano['nome']; ?></span>
                                            <span class="plan-price">R$ <?php echo number_format($plano['preco'], 2, ',', '.'); ?>/mês</span>
                                            <span class="plan-description"><?php echo $plano['descricao']; ?></span>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="secure-badge">
                                <span>🔒 Pagamento Seguro com Criptografia</span>
                            </div>
                        </section>

                        <!-- Seção: Detalhes do Pagamento -->
                        <section class="payment-details">
                            <h3 class="section-title">Detalhes do Pagamento</h3>
                            
                            <div class="form-group">
                                <label for="nome_cartao">Nome no Cartão *</label>
                                <input type="text" id="nome_cartao" name="nome_cartao" 
                                       placeholder="Como aparece no cartão" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="cpf_pagamento">CPF *</label>
                                <input type="text" id="cpf_pagamento" name="cpf_pagamento" 
                                       placeholder="000.000.000-00" required>
                            </div>

                            <div class="form-group">
                                <label for="numero_cartao">Número do Cartão *</label>
                                <input type="text" id="numero_cartao" name="numero_cartao" 
                                       placeholder="0000 0000 0000 0000" maxlength="19" required>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="data_expiracao">Data de Validade *</label>
                                    <input type="text" id="data_expiracao" name="data_expiracao" 
                                           placeholder="MM/YY" maxlength="5" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="cvv">CVV *</label>
                                    <input type="text" id="cvv" name="cvv" placeholder="123" 
                                           maxlength="4" required>
                                </div>
                            </div>

                            <div class="card-icons">
                                <span class="card-icon" title="Visa">💳 VISA</span>
                                <span class="card-icon" title="Mastercard">💳 MASTERCARD</span>
                                <span class="card-icon" title="Elo">💳 ELO</span>
                                <span class="card-icon" title="Hipercard">💳 HIPERCARD</span>
                            </div>
                            
                            <div class="payment-buttons">
                                <button type="submit" class="btn-primary btn-large">Finalizar Pagamento</button>
                                <button type="button" class="btn-secondary btn-large" onclick="voltarDashboard()">Cancelar</button>
                            </div>

                            <p class="payment-info">
                                Ao clicar em "Finalizar Pagamento", você concorda com nossos termos de serviço. 
                                Seu pagamento será processado de forma segura.
                            </p>
                        </section>

                    </form>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <footer></footer>

    <script>
        // Formatar número do cartão
        document.getElementById('numero_cartao').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formatted = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formatted;
        });

        // Formatar data de validade
        document.getElementById('data_expiracao').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 2) {
                value = value.slice(0, 2) + '/' + value.slice(2, 4);
            }
            e.target.value = value;
        });

        // Formatar CPF
        document.getElementById('cpf_pagamento').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
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

        // Permitir apenas números no CVV
        document.getElementById('cvv').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });

        // Validar formulário ao enviar
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const plano = document.querySelector('input[name="plano"]:checked');
            const nome = document.getElementById('nome_cartao').value.trim();
            const cpf = document.getElementById('cpf_pagamento').value.trim();
            const numero = document.getElementById('numero_cartao').value.trim();
            const data = document.getElementById('data_expiracao').value.trim();
            const cvv = document.getElementById('cvv').value.trim();

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
            window.location.href = 'index.php';
        }
    </script>
</body>
</html>
