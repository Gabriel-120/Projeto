<?php

function instrutorController(): void
{
    requireFuncionario('instrutor');
    
    $userId = $_SESSION['user_id'] ?? 0;
    $action = $_GET['action'] ?? '';
    
    // Obtém dados do funcionário/instrutor
    $pdo = Connect::conectar();
    $stmt = $pdo->prepare('SELECT f.*, u.nome, u.email FROM Funcionarios f 
                           JOIN Usuarios u ON f.id_usuario = u.id_usuario 
                           WHERE f.id_usuario = :id');
    $stmt->execute([':id' => $userId]);
    $instrutor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$instrutor) {
        flash('Acesso negado', 'error');
        header('Location: /');
        exit;
    }
    
    $data = [
        'instrutor' => $instrutor,
    ];
    
    // Verifica primeira vez e senha precisa ser alterada
    $stmtUser = $pdo->prepare('SELECT data_nascimento FROM Usuarios WHERE id_usuario = :id');
    $stmtUser->execute([':id' => $userId]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
    
    if (is_null($user['data_nascimento'])) {
        $data['alerta_cadastro'] = 'Seu cadastro não está completo. Acesse as configurações para completar.';
    }
    
    switch ($action) {
        case 'criar_aula':
            handleCriarAulaInstrutor($userId);
            break;
        case 'avaliar_aluno':
            handleAvaliarAluno($userId);
            break;
    }
    
    // Lista aulas do instrutor
    $sqlAulas = 'SELECT a.*, m.nome_modalidade, f.nome_filial, COUNT(DISTINCT aa.id_aluno) as total_alunos
                 FROM Aulas a
                 LEFT JOIN Modalidades m ON a.id_modalidade = m.id_modalidade
                 LEFT JOIN Filiais f ON a.id_filial = f.id_filial
                 LEFT JOIN Aulas_Aluno aa ON a.id_aula = aa.id_aula
                 WHERE a.id_funcionario = :func_id
                 GROUP BY a.id_aula
                 ORDER BY a.dia_aula DESC';
    $stmtAulas = $pdo->prepare($sqlAulas);
    $stmtAulas->execute([':func_id' => $instrutor['id_funcionario']]);
    $data['aulas'] = $stmtAulas->fetchAll(PDO::FETCH_ASSOC);
    
    // Lista modalidades para criar aula
    $stmtMod = $pdo->prepare('SELECT * FROM Modalidades');
    $stmtMod->execute();
    $data['modalidades'] = $stmtMod->fetchAll(PDO::FETCH_ASSOC);
    
    // Lista filiais
    $stmtFilial = $pdo->prepare('SELECT * FROM Filiais');
    $stmtFilial->execute();
    $data['filiais'] = $stmtFilial->fetchAll(PDO::FETCH_ASSOC);
    
    render('instrutor/dashboard', 'Dashboard do Instrutor', $data);
}

function handleCriarAulaInstrutor(int $userId): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
    
    $nomeAula = trim($_POST['nome_aula'] ?? '');
    $diaAula = $_POST['dia_aula'] ?? '';
    $qtdPessoas = (int) ($_POST['quantidade_pessoas'] ?? 0);
    $descricao = trim($_POST['descricao'] ?? '');
    $idModalidade = (int) ($_POST['id_modalidade'] ?? 0);
    $idFilial = (int) ($_POST['id_filial'] ?? 0);
    
    if ($nomeAula === '' || $diaAula === '' || $qtdPessoas <= 0 || $idModalidade <= 0 || $idFilial <= 0) {
        flash('Todos os campos são obrigatórios', 'error');
        return;
    }
    
    try {
        $pdo = Connect::conectar();
        
        // Obtém o id_funcionario do instrutor
        $stmt = $pdo->prepare('SELECT id_funcionario FROM Funcionarios WHERE id_usuario = :id');
        $stmt->execute([':id' => $userId]);
        $func = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$func) {
            flash('Erro: funcionário não encontrado', 'error');
            return;
        }
        
        $stmtInsert = $pdo->prepare('INSERT INTO Aulas (nome_aula, dia_aula, quantidade_pessoas, descricao, id_funcionario, id_modalidade, id_filial)
                                     VALUES (:nome, :dia, :qtd, :desc, :func, :mod, :filial)');
        $stmtInsert->execute([
            ':nome' => $nomeAula,
            ':dia' => $diaAula,
            ':qtd' => $qtdPessoas,
            ':desc' => $descricao,
            ':func' => $func['id_funcionario'],
            ':mod' => $idModalidade,
            ':filial' => $idFilial
        ]);
        
        flash('Aula criada com sucesso!', 'success');
    } catch (Exception $e) {
        flash('Erro ao criar aula: ' . $e->getMessage(), 'error');
    }
}

function handleAvaliarAluno(int $userId): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
    
    $idAluno = (int) ($_POST['id_aluno'] ?? 0);
    $nota = (float) ($_POST['nota'] ?? 0);
    $comentarios = trim($_POST['comentarios'] ?? '');
    
    if ($idAluno <= 0 || $nota < 0 || $nota > 10) {
        flash('Dados inválidos para avaliação', 'error');
        return;
    }
    
    try {
        $pdo = Connect::conectar();
        
        $stmt = $pdo->prepare('SELECT id_funcionario FROM Funcionarios WHERE id_usuario = :id');
        $stmt->execute([':id' => $userId]);
        $func = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmtEval = $pdo->prepare('INSERT INTO Avaliacoes (comentarios, nota, id_aluno, id_funcionario)
                                   VALUES (:com, :nota, :aluno, :func)
                                   ON DUPLICATE KEY UPDATE nota = :nota, comentarios = :com');
        $stmtEval->execute([
            ':com' => $comentarios,
            ':nota' => $nota,
            ':aluno' => $idAluno,
            ':func' => $func['id_funcionario']
        ]);
        
        flash('Avaliação registrada!', 'success');
    } catch (Exception $e) {
        flash('Erro ao salvar avaliação: ' . $e->getMessage(), 'error');
    }
}

?>
