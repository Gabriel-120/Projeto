<?php
require_once __DIR__ . '/../app/helpers/loadModels.php';

// Espera-se que o usuário esteja logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$mensagem = '';
$plano = isset($_GET['plano']) ? trim($_GET['plano']) : (isset($_POST['plano'])?$_POST['plano']: '');
$preco = isset($_GET['preco']) ? floatval($_GET['preco']) : (isset($_POST['preco'])?floatval($_POST['preco']):0.0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {
    $res = PagamentoModel::criarPagamentoPorUsuario((int)$_SESSION['usuario_id'], $plano, $preco);
    if ($res['sucesso']) {
        $mensagem = 'Pagamento registrado com sucesso.';
    } else {
        $mensagem = 'Erro ao registrar pagamento.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pagamento</title>
</head>
<body>
    <h2>Página de Pagamento</h2>
    <?php if ($plano): ?>
        <div style="float:left;width:40%;border:1px solid #ddd;padding:10px;margin-right:10px;">
            <h3>Plano selecionado</h3>
            <p><strong>Plano:</strong> <?php echo htmlspecialchars($plano); ?></p>
            <p><strong>Preço:</strong> R$ <?php echo number_format($preco,2,',','.'); ?></p>
        </div>
    <?php else: ?>
        <div style="float:left;width:40%;border:1px solid #ddd;padding:10px;margin-right:10px;">Nenhum plano selecionado.</div>
    <?php endif; ?>

    <div style="overflow:hidden">
        <h3>Formulário de pagamento</h3>
        <?php if ($mensagem): ?>
            <div style="color:green"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <form method="POST" action="pagamento.php">
            <input type="hidden" name="plano" value="<?php echo htmlspecialchars($plano); ?>">
            <input type="hidden" name="preco" value="<?php echo htmlspecialchars($preco); ?>">
            <label>Método</label><br>
            <select name="metodo">
                <option value="cartao">Cartão</option>
                <option value="boleto">Boleto</option>
            </select>
            <br><br>
            <button type="submit" name="confirmar">Confirmar pagamento</button>
        </form>
    </div>
</body>
</html>
