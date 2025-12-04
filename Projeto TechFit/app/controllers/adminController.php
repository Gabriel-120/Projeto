<?php

require_once __DIR__ . '/controller.php';
require_once __DIR__ . '/../models/Planos.php';

function adminController(): void
{
    requireFuncionario();

    $action = $_POST['action'] ?? '';
    // messages per tab to avoid global flashes that force tab changes
    $messages = [
        'usuarios' => ['errors'=>[], 'success'=>null],
        'funcionarios' => ['errors'=>[], 'success'=>null],
        'planos' => ['errors'=>[], 'success'=>null],
        'aulas' => ['errors'=>[], 'success'=>null],
        'comunicados' => ['errors'=>[], 'success'=>null]
    ];

    // track which tab was active (default 'usuarios') so we can keep the UI on the same tab after POST
    $tab = 'usuarios';

    // CRUD simples de usuários
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        switch ($action) {
                // (removed quick message saving - not needed)
            case 'add_user':
                $tab = 'usuarios';
                $nome  = trim($_POST['nome'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $cpf   = trim($_POST['cpf'] ?? '');
                $tipo  = trim($_POST['tipo'] ?? 'aluno');
                $senha = $_POST['senha'] ?? '';

                if ($nome === '' || $email === '' || $cpf === '' || $senha === '') {
                    $messages[$tab]['errors'][] = 'Todos os campos são obrigatórios.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $messages[$tab]['errors'][] = 'Email inválido.';
                } else {
                    // Criar usuário diretamente com PDO
                    $pdo = Connect::conectar();
                    $sql = "INSERT INTO Usuarios (nome, email, cpf, data_nascimento, senha_hash, tipo, avatar) VALUES (:nome, :email, :cpf, NULL, :senha_hash, :tipo, '/assets/images/upload/pfp/avatar.png')";
                    $stmt = $pdo->prepare($sql);
                    $ok = $stmt->execute([
                        ':nome' => $nome,
                        ':email' => $email,
                        ':cpf' => $cpf,
                        ':senha_hash' => password_hash($senha, PASSWORD_DEFAULT),
                        ':tipo' => $tipo
                    ]);

                    if ($ok) {
                        $messages[$tab]['success'] = 'Usuário criado com sucesso.';
                    } else {
                        $messages[$tab]['errors'][] = 'Erro ao criar usuário.';
                    }
                }
                break;

            // --- Funcionários (tabela Funcionarios) ---
            case 'add_funcionario':
                $tab = 'funcionarios';
                $nome = trim($_POST['nome_func'] ?? '');
                $cpf  = trim($_POST['cpf_func'] ?? '');
                $salario = floatval($_POST['salario'] ?? 0);
                $cargo = trim($_POST['cargo'] ?? '');
                $carga = trim($_POST['carga_horaria'] ?? '');
                // aceitarmos entradas como '40' ou '40h'
                $carga_val = intval(preg_replace('/[^0-9]/','',$carga));
                $id_usuario = intval($_POST['id_usuario_func'] ?? 0);
                if ($nome === '' || $cpf === '' || $id_usuario <= 0) {
                    $messages[$tab]['errors'][] = 'Dados de funcionário inválidos.';
                } else {
                    $pdo = Connect::conectar();
                    $stmt = $pdo->prepare('INSERT INTO Funcionarios (salario, carga_horaria, cargo, id_usuario) VALUES (:salario, :carga, :cargo, :id_usuario)');
                    $ok = $stmt->execute([':salario'=>$salario, ':carga'=>$carga_val, ':cargo'=>$cargo, ':id_usuario'=>$id_usuario]);
                    if ($ok) $messages[$tab]['success'] = 'Funcionário criado.'; else $messages[$tab]['errors'][] = 'Erro ao criar funcionário';
                }
                break;

            case 'delete_funcionario':
                $tab = 'funcionarios';
                $id = intval($_POST['id_funcionario'] ?? 0);
                if ($id <= 0) $messages[$tab]['errors'][] = 'ID inválido'; else {
                    $pdo = Connect::conectar();
                    $ok = $pdo->prepare('DELETE FROM Funcionarios WHERE id_funcionario = :id')->execute([':id'=>$id]);
                    if ($ok) $messages[$tab]['success'] = 'Funcionário removido.'; else $messages[$tab]['errors'][]='Erro ao remover';
                }
                break;

            // --- Planos ---
            case 'add_plano':
                $tab = 'planos';
                $nome = trim($_POST['nome_plano'] ?? '');
                $descricao = trim($_POST['descricao_plano'] ?? '');
                $preco = floatval($_POST['preco'] ?? 0);
                $duracao = intval($_POST['duracao'] ?? 30);
                if ($nome === '' || $preco <= 0) $messages[$tab]['errors'][] = 'Dados inválidos para plano';
                else { if (Planos::create($nome,$descricao,$preco,$duracao)) $messages[$tab]['success']='Plano criado.'; else $messages[$tab]['errors'][]='Erro ao criar plano'; }
                break;

            // --- Modalidades ---
            case 'add_modalidade':
                $tab = 'modalidades';
                $nome = trim($_POST['nome_modalidade'] ?? '');
                $descricao = trim($_POST['descricao_modalidade'] ?? '');
                if ($nome === '') $messages[$tab]['errors'][] = 'Nome da modalidade obrigatório';
                else {
                    $pdo = Connect::conectar();
                    $stmt = $pdo->prepare('INSERT INTO Modalidades (nome_modalidade, descricao) VALUES (:nome, :descricao)');
                    $ok = $stmt->execute([':nome'=>$nome, ':descricao'=>$descricao]);
                    if ($ok) $messages[$tab]['success'] = 'Modalidade criada.'; else $messages[$tab]['errors'][] = 'Erro ao criar modalidade';
                }
                break;

            case 'edit_modalidade':
                $tab = 'modalidades';
                $id = intval($_POST['id_modalidade'] ?? 0);
                $nome = trim($_POST['nome_modalidade'] ?? '');
                $descricao = trim($_POST['descricao_modalidade'] ?? '');
                if ($id<=0 || $nome==='') $messages[$tab]['errors'][]='Dados inválidos para modalidade'; else {
                    $pdo = Connect::conectar();
                    $stmt = $pdo->prepare('UPDATE Modalidades SET nome_modalidade = :nome, descricao = :descricao WHERE id_modalidade = :id');
                    $ok = $stmt->execute([':nome'=>$nome, ':descricao'=>$descricao, ':id'=>$id]);
                    if ($ok) $messages[$tab]['success']='Modalidade atualizada.'; else $messages[$tab]['errors'][]='Erro ao atualizar modalidade';
                }
                break;

            case 'delete_modalidade':
                $tab = 'modalidades';
                $id = intval($_POST['id_modalidade'] ?? 0);
                if ($id<=0) $messages[$tab]['errors'][]='ID inválido'; else { $ok = Connect::conectar()->prepare('DELETE FROM Modalidades WHERE id_modalidade = :id')->execute([':id'=>$id]); if($ok) $messages[$tab]['success']='Modalidade removida.'; else $messages[$tab]['errors'][]='Erro ao remover modalidade'; }
                break;

            // --- Filiais ---
            case 'add_filial':
                $tab = 'filiais';
                $nome = trim($_POST['nome_filial'] ?? '');
                $end = trim($_POST['endereco_filial'] ?? '');
                $tel = trim($_POST['telefone_filial'] ?? '');
                if ($nome === '') $messages[$tab]['errors'][]='Nome da filial obrigatório'; else {
                    $pdo = Connect::conectar();
                    $stmt = $pdo->prepare('INSERT INTO Filiais (nome_filial, endereco, telefone) VALUES (:nome, :endereco, :telefone)');
                    $ok = $stmt->execute([':nome'=>$nome, ':endereco'=>$end, ':telefone'=>$tel]);
                    if ($ok) $messages[$tab]['success']='Filial criada.'; else $messages[$tab]['errors'][]='Erro ao criar filial';
                }
                break;

            case 'edit_filial':
                $tab = 'filiais';
                $id = intval($_POST['id_filial'] ?? 0);
                $nome = trim($_POST['nome_filial'] ?? '');
                $end = trim($_POST['endereco_filial'] ?? '');
                $tel = trim($_POST['telefone_filial'] ?? '');
                if ($id<=0 || $nome==='') $messages[$tab]['errors'][]='Dados inválidos para filial'; else {
                    $pdo = Connect::conectar();
                    $stmt = $pdo->prepare('UPDATE Filiais SET nome_filial = :nome, endereco = :endereco, telefone = :telefone WHERE id_filial = :id');
                    $ok = $stmt->execute([':nome'=>$nome, ':endereco'=>$end, ':telefone'=>$tel, ':id'=>$id]);
                    if ($ok) $messages[$tab]['success']='Filial atualizada.'; else $messages[$tab]['errors'][]='Erro ao atualizar filial';
                }
                break;

            case 'delete_filial':
                $tab = 'filiais';
                $id = intval($_POST['id_filial'] ?? 0);
                if ($id<=0) $messages[$tab]['errors'][]='ID inválido'; else { $ok = Connect::conectar()->prepare('DELETE FROM Filiais WHERE id_filial = :id')->execute([':id'=>$id]); if($ok) $messages[$tab]['success']='Filial removida.'; else $messages[$tab]['errors'][]='Erro ao remover filial'; }
                break;

            case 'delete_plano':
                $tab = 'planos';
                $id = intval($_POST['id_plano'] ?? 0);
                if ($id<=0) $messages[$tab]['errors'][]='ID inválido'; else { if (Planos::delete($id)) $messages[$tab]['success']='Plano removido.'; else $messages[$tab]['errors'][]='Erro ao remover plano'; }
                break;

            case 'edit_plano':
                $tab = 'planos';
                $id = intval($_POST['id_plano'] ?? 0);
                $nome = trim($_POST['nome_plano'] ?? '');
                $descricao = trim($_POST['descricao_plano'] ?? '');
                $preco = floatval($_POST['preco'] ?? 0);
                $duracao = intval($_POST['duracao'] ?? 0);
                if ($id<=0 || $nome==='') $messages[$tab]['errors'][]='Dados inválidos'; else { if (Planos::update($id,$nome,$descricao,$preco,$duracao)) $messages[$tab]['success']='Plano atualizado.'; else $messages[$tab]['errors'][]='Erro ao atualizar plano'; }
                break;

            // --- Aulas ---
            case 'add_aula':
                $tab = 'aulas';
                $nomeA = trim($_POST['nome_aula'] ?? '');
                $dia = trim($_POST['dia_aula'] ?? '');
                $qtd = intval($_POST['quantidade'] ?? 0);
                $descricao = trim($_POST['descricao_aula'] ?? '');
                $id_func = intval($_POST['id_funcionario_aula'] ?? 0);
                $id_modal = intval($_POST['id_modalidade_aula'] ?? 0);
                $id_filial = intval($_POST['id_filial_aula'] ?? 0);
                if ($nomeA==='' || $dia==='') $messages[$tab]['errors'][]='Dados inválidos para aula'; else {
                    $pdo = Connect::conectar();
                    $stmt = $pdo->prepare('INSERT INTO Aulas (nome_aula, dia_aula, quantidade_pessoas, descricao, id_funcionario, id_modalidade, id_filial) VALUES (:nome,:dia,:qtd,:desc,:idf,:idm,:idfili)');
                    $ok = $stmt->execute([':nome'=>$nomeA,':dia'=>$dia,':qtd'=>$qtd,':desc'=>$descricao,':idf'=>$id_func,':idm'=>$id_modal,':idfili'=>$id_filial]);
                    if ($ok) $messages[$tab]['success']='Aula criada.'; else $messages[$tab]['errors'][]='Erro ao criar aula';
                }
                break;

            case 'delete_aula':
                $tab = 'aulas';
                $id = intval($_POST['id_aula'] ?? 0);
                if ($id<=0) $messages[$tab]['errors'][]='ID inválido'; else { $ok = Connect::conectar()->prepare('DELETE FROM Aulas WHERE id_aula = :id')->execute([':id'=>$id]); if($ok) $messages[$tab]['success']='Aula removida.'; else $messages[$tab]['errors'][]='Erro ao remover aula'; }
                break;

            case 'delete_user':
                $tab = 'usuarios';
                $id = (int)($_POST['id_usuario'] ?? 0);
                if ($id <= 0) {
                    $messages[$tab]['errors'][] = 'ID inválido.';
                } else {
                    $pdo = Connect::conectar();
                    $stmt = $pdo->prepare('DELETE FROM Usuarios WHERE id_usuario = :id');
                    $ok = $stmt->execute([':id' => $id]);
                    if ($ok) $messages[$tab]['success'] = 'Usuário removido.'; else $messages[$tab]['errors'][] = 'Erro ao remover.';
                }
                break;

            case 'edit_user':
                $tab = 'usuarios';
                $id = (int)($_POST['id_usuario'] ?? 0);
                $nome  = trim($_POST['nome'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $tipo  = trim($_POST['tipo'] ?? 'aluno');
                if ($id <= 0 || $nome === '' || $email === '') {
                    $messages[$tab]['errors'][] = 'Dados inválidos.';
                } else {
                    $pdo = Connect::conectar();
                    $stmt = $pdo->prepare('UPDATE Usuarios SET nome = :nome, email = :email, tipo = :tipo WHERE id_usuario = :id');
                    $ok = $stmt->execute([':nome' => $nome, ':email' => $email, ':tipo' => $tipo, ':id' => $id]);
                    if ($ok) $messages[$tab]['success'] = 'Usuário atualizado.'; else $messages[$tab]['errors'][] = 'Erro ao atualizar.';
                }
                break;
            // --- editar funcionário ---
            case 'edit_funcionario':
                $tab = 'funcionarios';
                $id = intval($_POST['id_funcionario'] ?? 0);
                $salario = floatval($_POST['salario'] ?? 0);
                $cargo = trim($_POST['cargo'] ?? '');
                $carga = trim($_POST['carga_horaria'] ?? '');
                $carga_val = intval(preg_replace('/[^0-9]/','',$carga));
                $id_usuario = intval($_POST['id_usuario_func'] ?? 0);
                if ($id <= 0) {
                    $messages[$tab]['errors'][] = 'ID inválido para edição.';
                } else {
                    $pdo = Connect::conectar();
                    $stmt = $pdo->prepare('UPDATE Funcionarios SET salario = :salario, carga_horaria = :carga, cargo = :cargo, id_usuario = :id_usuario WHERE id_funcionario = :id');
                    $ok = $stmt->execute([':salario'=>$salario, ':carga'=>$carga_val, ':cargo'=>$cargo, ':id_usuario'=>$id_usuario, ':id'=>$id]);
                    if ($ok) $messages[$tab]['success'] = 'Funcionário atualizado.'; else $messages[$tab]['errors'][] = 'Erro ao atualizar funcionário.';
                }
                break;
        }
        // After processing POST, redirect back to keep UI on the same tab and avoid resubmission
        if (!headers_sent()) {
            $redirect = '/admin/painel' . (!empty($tab) ? '?active_tab=' . urlencode($tab) : '');
            header('Location: ' . $redirect);
            exit;
        }
    }

    // buscar lista de usuários
    $pdo = Connect::conectar();
    $stmt = $pdo->query('SELECT id_usuario, nome, email, cpf, tipo FROM Usuarios ORDER BY id_usuario DESC');
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // buscar funcionarios
    $stmtF = $pdo->query('SELECT id_funcionario, salario, carga_horaria, cargo, id_usuario FROM Funcionarios ORDER BY id_funcionario DESC');
    $funcionarios = $stmtF->fetchAll(PDO::FETCH_ASSOC);

    // planos e aulas
    $planos = class_exists('Planos') ? Planos::getAll() : [];
    $stmtA = $pdo->query('SELECT A.*, M.nome_modalidade, F.nome_filial FROM Aulas A LEFT JOIN Modalidades M ON A.id_modalidade = M.id_modalidade LEFT JOIN Filiais F ON A.id_filial = F.id_filial ORDER BY A.dia_aula DESC');
    $aulas = $stmtA->fetchAll(PDO::FETCH_ASSOC);
    // modalidades
    $modalidades = method_exists('Modalidades','getModalidades') ? Modalidades::getModalidades() : [];
    // filiais
    $filiais = [];
    try {
        $stmtF = $pdo->query('SELECT id_filial, nome_filial, endereco, telefone FROM Filiais ORDER BY id_filial DESC');
        $filiais = $stmtF->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // ignore if table missing
    }

    $data = [
        'usuarios' => $usuarios,
        'funcionarios' => $funcionarios,
        'planos' => $planos,
        'aulas' => $aulas,
        'modalidades' => $modalidades,
    'filiais' => $filiais,
        // per-tab messages
        'messages' => $messages,
        'avisos' => method_exists('Aviso','getAllForAdmin') ? Aviso::getAllForAdmin() : [],
        'nomeTipo' => method_exists('Aviso','getTipoLabelsAvisos') ? Aviso::getTipoLabelsAvisos() : [],
        'avisoTipos' => method_exists('Aviso','getTipos') ? Aviso::getTipos() : []
    ];

    render('admin/painel', 'Painel Administrativo', $data);
}

?>