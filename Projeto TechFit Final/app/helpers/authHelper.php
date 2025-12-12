<?php 

function logout(){
    session_destroy();
    header('Location: /');
}

function requireFuncionario($cargoEspecifico = null)
{
    if (empty($_SESSION['user_id']) || ($_SESSION['user_tipo'] ?? null) !== 'funcionario') {
        http_response_code(403);
        echo "Acesso negado.";
        exit;
    }
    
    // Se um cargo específico foi solicitado, valida também
    if ($cargoEspecifico !== null) {
        $pdo = Connect::conectar();
        $stmt = $pdo->prepare('SELECT cargo FROM Funcionarios WHERE id_usuario = :id');
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $func = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$func || strtolower($func['cargo']) !== strtolower($cargoEspecifico)) {
            http_response_code(403);
            echo "Acesso negado para este cargo.";
            exit;
        }
    }
}

function verificarCadastroIncompleto(int $userId): bool
{
    $pdo = Connect::conectar();
    
    // Para alunos: verifica se tem dados completos em Alunos
    if ($_SESSION['user_tipo'] === 'aluno') {
        $stmt = $pdo->prepare('SELECT cadastro_completo FROM Alunos WHERE id_usuario = :id');
        $stmt->execute([':id' => $userId]);
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);
        return !($aluno['cadastro_completo'] ?? false);
    }
    
    // Para funcionários: verifica se data_nascimento está preenchida
    $stmt = $pdo->prepare('SELECT data_nascimento FROM Usuarios WHERE id_usuario = :id');
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return is_null($user['data_nascimento'] ?? null);
}

?>
