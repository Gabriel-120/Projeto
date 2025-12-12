<?php
require_once __DIR__ . '/../app/models/Connect.php';

try {
    $pdo = Connect::conectar();
    
    // Buscar usuário por email
    $stmt = $pdo->prepare('SELECT u.id_usuario, u.nome, u.email, u.cpf, u.tipo, u.senha_hash, f.id_funcionario, f.cargo FROM Usuarios u LEFT JOIN Funcionarios f ON u.id_usuario = f.id_usuario WHERE u.email = ?');
    $stmt->execute(['bruno.r@techfit.com']);
    
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario) {
        echo "✓ Usuário encontrado:\n";
        echo "  ID: " . $usuario['id_usuario'] . "\n";
        echo "  Nome: " . $usuario['nome'] . "\n";
        echo "  Email: " . $usuario['email'] . "\n";
        echo "  CPF: " . $usuario['cpf'] . "\n";
        echo "  Tipo: " . $usuario['tipo'] . "\n";
        echo "  Cargo: " . ($usuario['cargo'] ?? 'N/A') . "\n";
        echo "  ID Funcionário: " . ($usuario['id_funcionario'] ?? 'N/A') . "\n";
        echo "\n  Senha Hash: " . substr($usuario['senha_hash'], 0, 20) . "..." . "\n";
        
        // Testar senha padrão
        echo "\n  Testando senhas:\n";
        if (password_verify('Techfit123', $usuario['senha_hash'])) {
            echo "    ✓ Senha 'Techfit123' está CORRETA\n";
        } else {
            echo "    ✗ Senha 'Techfit123' está INCORRETA\n";
        }
        
        if (password_verify('techfit123', $usuario['senha_hash'])) {
            echo "    ✓ Senha 'techfit123' (minúscula) está CORRETA\n";
        } else {
            echo "    ✗ Senha 'techfit123' (minúscula) está INCORRETA\n";
        }
    } else {
        echo "✗ Usuário não encontrado com esse email\n";
        echo "\nBuscando todos os usuários com 'bruno' no email:\n";
        $stmt = $pdo->prepare("SELECT u.id_usuario, u.nome, u.email, u.cpf, u.tipo FROM Usuarios u WHERE u.email LIKE '%bruno%' OR u.nome LIKE '%bruno%'");
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($resultados) {
            foreach ($resultados as $r) {
                echo "  - " . $r['nome'] . " (" . $r['email'] . ")\n";
            }
        } else {
            echo "  Nenhum usuário com 'bruno' encontrado.\n";
        }
    }
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
}
?>
