<?php
/**
 * Helper para geração automática de usuários de funcionários
 */

function gerarEmailDoCPF(string $nome): string {
    // Remove acentos e caracteres especiais
    $nome = iconv('UTF-8', 'ASCII//TRANSLIT', $nome);
    $nome = preg_replace('/[^a-zA-Z\s]/', '', $nome);
    
    // Pega primeira letra de cada palavra
    $partes = explode(' ', trim($nome));
    $email = $partes[0] . '.';
    
    if (count($partes) > 1) {
        $email .= substr($partes[count($partes) - 1], 0, 1);
    } else {
        $email .= substr($partes[0], 1, 1);
    }
    
    $email = strtolower($email) . '@techfit.com';
    return $email;
}

function criarUsuarioFuncionario(string $nome, string $cpf, string $cargo): ?int {
    try {
        $pdo = Connect::conectar();
        
        // Gera email baseado no nome
        $email = gerarEmailDoCPF($nome);
        
        // Verifica se email já existe
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM Usuarios WHERE email = :email');
        $stmt->execute([':email' => $email]);
        if ((int)$stmt->fetchColumn() > 0) {
            // Se já existe, tenta adicionar um número
            $counter = 1;
            $baseEmail = str_replace('@techfit.com', '', $email);
            while (true) {
                $email = $baseEmail . $counter . '@techfit.com';
                $stmt = $pdo->prepare('SELECT COUNT(*) FROM Usuarios WHERE email = :email');
                $stmt->execute([':email' => $email]);
                if ((int)$stmt->fetchColumn() === 0) break;
                $counter++;
            }
        }
        
        // Cria usuário com senha padrão
        $senhaHash = password_hash('Techfit123', PASSWORD_DEFAULT);
        $tipo = 'funcionario';
        
        $stmt = $pdo->prepare('INSERT INTO Usuarios (nome, email, cpf, tipo, senha_hash, avatar) 
                              VALUES (:nome, :email, :cpf, :tipo, :senha_hash, :avatar)');
        $stmt->execute([
            ':nome' => $nome,
            ':email' => $email,
            ':cpf' => $cpf,
            ':tipo' => $tipo,
            ':senha_hash' => $senhaHash,
            ':avatar' => '/images/upload/pfp/avatar.png'
        ]);
        
        $usuarioId = (int)$pdo->lastInsertId();
        return $usuarioId;
        
    } catch (Exception $e) {
        error_log('Erro ao criar usuário de funcionário: ' . $e->getMessage());
        return null;
    }
}
?>
