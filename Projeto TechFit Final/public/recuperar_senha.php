<?php
require_once __DIR__ . '/../app/helpers/loadModels.php';

$mensagem_erro = '';
$mensagem_sucesso = '';
$etapa = 'verificacao';
$token = '';

if (isset($_GET['token'])) {
    $token = trim($_GET['token']);
    if (RecuperacaoModel::tokenValido($token)) {
        $etapa = 'reset';
    } else {
        $mensagem_erro = 'Token inválido ou expirado.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['etapa']) && $_POST['etapa'] === 'verificacao') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $cpf = isset($_POST['cpf']) ? trim($_POST['cpf']) : '';

    if (empty($email) || empty($cpf)) {
        $mensagem_erro = 'Email e CPF são obrigatórios.';
    } else {
        $pdo = Connect::conectar();
        $sql = "SELECT id_usuario, cpf FROM Usuarios WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $mensagem_erro = 'Email não encontrado.';
        } elseif ($user['cpf'] !== $cpf) {
            $mensagem_erro = 'CPF não corresponde ao email informado.';
        } else {
            $token = RecuperacaoModel::gerarToken();
            $exp = date('Y-m-d H:i:s', strtotime('+1 hour'));
            RecuperacaoModel::criarToken((int)$user['id_usuario'], $token, $exp);
            $mensagem_sucesso = 'Verificação bem-sucedida! Um link de recuperação foi gerado.';
            header('Refresh: 2; url=recuperar_senha.php?token=' . urlencode($token));
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['etapa']) && $_POST['etapa'] === 'reset') {
    $token = isset($_POST['token']) ? trim($_POST['token']) : '';
    $nova = isset($_POST['nova_senha']) ? $_POST['nova_senha'] : '';
    $conf = isset($_POST['confirma_senha']) ? $_POST['confirma_senha'] : '';

    if ($nova !== $conf) {
        $mensagem_erro = 'As senhas não conferem.';
    } elseif (strlen($nova) < 6) {
        $mensagem_erro = 'A senha deve ter no mínimo 6 caracteres.';
    } else {
        $rec = RecuperacaoModel::buscarPorToken($token);
        if (!$rec) {
            $mensagem_erro = 'Token inválido.';
        } else {
            $hash = password_hash($nova, PASSWORD_DEFAULT);
            $pdo = Connect::conectar();
            $sql = "UPDATE Usuarios SET senha_hash = :senha WHERE id_usuario = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':senha' => $hash, ':id' => $rec['usuario_id']]);
            RecuperacaoModel::marcarUtilizado($token);
            $mensagem_sucesso = 'Senha redefinida com sucesso.';
            header('Refresh: 2; url=login.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Recuperar Senha</title>
</head>
<body>
    <h2>Recuperar Senha</h2>
    <?php if (!empty($mensagem_erro)): ?>
        <div style="color:#b00020"><?php echo htmlspecialchars($mensagem_erro); ?></div>
    <?php endif; ?>
    <?php if (!empty($mensagem_sucesso)): ?>
        <div style="color:green"><?php echo htmlspecialchars($mensagem_sucesso); ?></div>
    <?php else: ?>
        <?php if ($etapa === 'verificacao'): ?>
            <form method="POST" action="recuperar_senha.php">
                <input type="hidden" name="etapa" value="verificacao">
                <label>Email</label><br>
                <input type="email" name="email" required><br>
                <label>CPF</label><br>
                <input type="text" name="cpf" required><br>
                <button type="submit">Verificar</button>
            </form>
        <?php else: ?>
            <form method="POST" action="recuperar_senha.php">
                <input type="hidden" name="etapa" value="reset">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <label>Nova senha</label><br>
                <input type="password" name="nova_senha" required><br>
                <label>Confirmar senha</label><br>
                <input type="password" name="confirma_senha" required><br>
                <button type="submit">Redefinir</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
