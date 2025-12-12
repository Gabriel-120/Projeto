<?php
require_once __DIR__ . '/../app/helpers/loadModels.php';
// garantir sessão ativa
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// verificar autenticação (aceitar user_id ou usuario_id)
$uid = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : (isset($_SESSION['usuario_id']) ? (int)$_SESSION['usuario_id'] : 0);
if ($uid <= 0) {
    $return = urlencode($_SERVER['REQUEST_URI']);
    header('Location: login.php?return_to=' . $return);
    exit;
}

require_once __DIR__ . '/../app/models/Planos.php';
require_once __DIR__ . '/../app/models/PagamentoModel.php';

$mensagem_erro = '';
$mensagem_sucesso = '';

$planos = Planos::getAll();
$planosById = [];
foreach ($planos as $p) $planosById[(int)$p['id_plano']] = $p;

$selectedId = isset($_GET['id_plano']) ? intval($_GET['id_plano']) : 0;
if ($selectedId === 0 && isset($_GET['plano'])) {
    // tentar casar por nome
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
            $planoNome = $planosById[$postId]['nome_plano'];
            $planoPreco = (float)$planosById[$postId]['preco'];
            $res = PagamentoModel::criarPagamentoPorUsuario($uid, $planoNome, $planoPreco, 'plano');
            if (!empty($res['sucesso'])) {
                // vincular plano ao aluno na tabela Planos_Aluno
                $pagamentoId = $res['pagamento_id'] ?? 0;
                $idAluno = $res['id_aluno'] ?? null;
                if ($idAluno) {
                    try {
                        $pdo = Connect::conectar();
                        $duracao = intval($planosById[$postId]['duracao'] ?? 30);
                        $dataInicio = date('Y-m-d');
                        $dataFim = date('Y-m-d', strtotime("+{$duracao} days"));
                        $status = 'ativo';
                        $ins = $pdo->prepare('INSERT INTO Planos_Aluno (id_aluno, id_plano, data_inicio, data_fim, status) VALUES (:id_aluno, :id_plano, :data_inicio, :data_fim, :status)');
                        $ins->execute([
                            ':id_aluno' => $idAluno,
                            ':id_plano' => $postId,
                            ':data_inicio' => $dataInicio,
                            ':data_fim' => $dataFim,
                            ':status' => $status
                        ]);
                    } catch (Exception $e) {
                        // não falhar o pagamento por falha secundária; log se precisar
                    }
                }
                // pagamento ok: redireciona para a página inicial
                header('Location: /');
                exit;
            } else {
                $mensagem_erro = $res['erro'] ?? 'Erro ao processar pagamento.';
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
    <link rel="stylesheet" href="/assets/css/pagamento.css">
    <title>TechFit - Planos e Pagamento</title>
</head>
<body>
    <div class="container">
        <main>
            <div class="payment-container">
                <header class="payment-header">
                    <div class="logo">
                        <img src="/assets/images/logo.png" alt="Logo TechFit">
                    </div>
                    <h2>Escolha seu Plano</h2>
                    <p class="header-subtitle">Bem-vindo, <?php echo htmlspecialchars($usuario_nome); ?>!</p>
                </header>

                <?php if (!empty($mensagem_erro)): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($mensagem_erro); ?></div>
                <?php endif; ?>

                <?php if (!empty($mensagem_sucesso)): ?>
                    <div class="alert alert-success">✓ <?php echo htmlspecialchars($mensagem_sucesso); ?></div>
                <?php else: ?>

                    <form class="payment-body" method="POST" action="/pagamentos.php" id="paymentForm">

                        <section class="plan-selection">
                            <h3 class="section-title">Selecione seu plano</h3>
                            <div class="plan-options">
                                <?php foreach ($planos as $p):
                                    $id = (int)$p['id_plano'];
                                    $checked = ($selectedId && $selectedId === $id) ? 'checked' : '';
                                ?>
                                <label class="plan-option" for="plano_<?php echo $id; ?>">
                                    <input type="radio" id="plano_<?php echo $id; ?>" name="plano" value="<?php echo $id; ?>" <?php echo $checked; ?> required>
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
                                <span class="card-icon" title="Visa">💳 VISA</span>
                                <span class="card-icon" title="Mastercard">💳 MASTERCARD</span>
                                <span class="card-icon" title="Elo">💳 ELO</span>
                                <span class="card-icon" title="Hipercard">💳 HIPERCARD</span>
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
        </main>
    </div>

    <script>
        // same JS as in Gabriel's file (formatters + validation)
        const numEl = document.getElementById('numero_cartao');
        if (numEl) numEl.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '');
            let formatted = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formatted;
        });

        const dataEl = document.getElementById('data_expiracao');
        if (dataEl) dataEl.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 2) value = value.slice(0,2) + '/' + value.slice(2,4);
            e.target.value = value;
        });

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

        const cvvEl = document.getElementById('cvv');
        if (cvvEl) cvvEl.addEventListener('input', function(e) { e.target.value = e.target.value.replace(/\D/g, ''); });

        const form = document.getElementById('paymentForm');
        if (form) form.addEventListener('submit', function(e) {
            const plano = document.querySelector('input[name="plano"]:checked');
            const nome = document.getElementById('nome_cartao')?.value.trim();
            const cpf = document.getElementById('cpf_pagamento')?.value.trim();
            const numero = document.getElementById('numero_cartao')?.value.trim();
            const data = document.getElementById('data_expiracao')?.value.trim();
            const cvv = document.getElementById('cvv')?.value.trim();
            if (!plano) { e.preventDefault(); alert('Selecione um plano para continuar!'); return false; }
            if (!nome || !cpf || !numero || !data || !cvv) { e.preventDefault(); alert('Preencha todos os campos obrigatórios!'); return false; }
            if (!data.includes('/') || data.split('/')[0].length !== 2) { e.preventDefault(); alert('Data de validade inválida! Use o formato MM/YY'); return false; }
            const numeros_cartao = numero.replace(/\s/g, '');
            if (numeros_cartao.length < 13) { e.preventDefault(); alert('Número de cartão inválido!'); return false; }
            if (cvv.length < 3) { e.preventDefault(); alert('CVV inválido!'); return false; }
        });

        function voltarDashboard() { window.location.href = '/'; }
    </script>
</body>
</html>
