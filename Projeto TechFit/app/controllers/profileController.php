<?php

require_once __DIR__ . '/agendaController.php';
function profileController(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit;
    }

    $usuario = Usuario::getUsuarioCompleto($_SESSION['user_id']);
    if (!$usuario) {
        // Usuário não encontrado
        flash("Usuario não encontrado", "error");
        session_destroy();
        header('Location: /');
        exit;
    }

    $currPage = $_GET['page'] ?? '';
    $act = $_POST['action'] ?? '';
    $data = [
        'user_pfp'   => $usuario['user_avatar'],
        'user_name'  => $usuario['user_name'],
        'user_tipo'  => ucfirst($usuario['user_tipo']),
        'headExtras' => <<<HTML
            <link rel="stylesheet" href="./assets/css/profile.css" />
            <link rel="stylesheet" href="./assets/css/utility.css"/>
        HTML,
        'currPage' => $currPage 
    ];

    // verificar se o usuário já possui registro na tabela Alunos (somente para tipo aluno)
    $needs_complete = false;
    if (strtolower($usuario['user_tipo']) === 'aluno') {
        $al = Aluno::getAlunoByUserID($usuario['user_id']);
        if (!$al) {
            $needs_complete = true;
        } else {
            // considerar incompleto se campos essenciais estiverem vazios ou com placeholder
            $genero = trim(strval($al['genero'] ?? ''));
            $endereco = trim(strval($al['endereco'] ?? ''));
            $telefone = trim(strval($al['telefone'] ?? ''));
            if ($genero === '' || strtolower($genero) === 'n/d' || $endereco === '' || $telefone === '') {
                $needs_complete = true;
            }
        }
    }
    $data['needs_complete'] = $needs_complete;

    // Preparando os dados para a sub-visão.
    switch ($currPage) {
        case 'agenda':
            $data['subView'] = 'agendaView.php';
            $pageData = loadAgendaData($_SESSION['user_id']);
            break;

        case 'avaliacao':
            $pageData = ['message' => '📊 Avaliação física em desenvolvimento.'];
            break;
        case 'frequencia':
            $pageData = ['message' => '📈 Frequência em desenvolvimento.'];
            break;

        case 'configuracao':
            $pageData = ['message' => '⚙️ Configurações em desenvolvimento.'];
            break;

        default:
            $data['subView'] = 'partials/placeholderView.php';
            $pageData = ['message' => 'Bem-vindo à sua página de perfil!'];
            break;
    }

    // nova página para concluir cadastro
    if ($currPage === 'concluir') {
        $data['subView'] = 'partials/completeCadastro.php';
        // fornecer dados do usuário e aluno para preencher o formulário
        $aluno = Aluno::getAlunoByUserID($usuario['user_id']);
        $pageData = [
            'user_email' => $usuario['user_email'] ?? '',
            'user_cpf' => $usuario['user_cpf'] ?? '' ,
            'aluno' => $aluno ?? []
        ];
    }

    switch ($act){
        case 'concluir_cadastro':
            // dados do formulário
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $cpf = trim($_POST['cpf'] ?? '');
            $genero = trim($_POST['genero'] ?? '');
            $endereco = trim($_POST['endereco'] ?? '');
            $telefone = trim($_POST['telefone'] ?? '');

            // atualizar Usuarios quando necessário
            if ($nome !== $usuario['user_name'] || $email !== $usuario['user_email'] || ($cpf !== ($usuario['user_cpf'] ?? ''))) {
                // atualizar email (há método) e nome/cpf diretamente
                if ($email !== $usuario['user_email']) {
                    Usuario::changeEmail($usuario['user_id'], $email);
                }
                $pdo = Connect::conectar();
                $upd = $pdo->prepare('UPDATE Usuarios SET nome = :nome, email = :email, cpf = :cpf WHERE id_usuario = :id');
                $upd->execute([':nome'=>$nome, ':email'=>$email, ':cpf'=>$cpf, ':id'=>$usuario['user_id']]);
                // refresh usuario array to reflect changes for later logic
                $usuario = Usuario::getUsuarioCompleto($usuario['user_id']);
            }

            // criar ou atualizar Alunos
            $aluno = Aluno::getAlunoByUserID($usuario['user_id']);
            if ($aluno) {
                $pdo = Connect::conectar();
                $u = $pdo->prepare('UPDATE Alunos SET genero = :genero, endereco = :endereco, telefone = :telefone WHERE id_aluno = :id_aluno');
                $u->execute([':genero'=>$genero, ':endereco'=>$endereco, ':telefone'=>$telefone, ':id_aluno'=>$aluno['id_aluno']]);
            } else {
                $pdo = Connect::conectar();
                $i = $pdo->prepare('INSERT INTO Alunos (genero, endereco, telefone, id_usuario) VALUES (:genero, :endereco, :telefone, :id_usuario)');
                $i->execute([':genero'=>$genero, ':endereco'=>$endereco, ':telefone'=>$telefone, ':id_usuario'=>$usuario['user_id']]);
            }

            // atualizar flag
            $data['needs_complete'] = false;
            flash('Cadastro concluído com sucesso.', 'success');
            break;
        case 'cancelar':
            $ag_id = $_POST['agendamento_id'];
           
            $aluno = Aluno::getAlunoByUserID($_SESSION["user_id"]);
            if (!$aluno) {
                flash('Não foi possível cancelar: cadastro de aluno incompleto. Complete seu cadastro no perfil.', 'error');
                break;
            }
            $id_aluno = $aluno["id_aluno"];
            cancelarAgendamento($ag_id, $id_aluno);
    }

    $data = array_merge($data, $pageData);

    render('profileView', 'Perfil', $data);
}

/**
 * Função privada para buscar os dados da agenda.
 * 
 *
 * @param int $id_aluno
 * @return array
 */
function loadAgendaData(int $id_usuario): array
{
    $aluno = Aluno::getAlunoByUserID($id_usuario);
    if (!$aluno) {
        // Sem aluno vinculado: retornar estruturas vazias para evitar erros na view
        return [
            'modalidadeSelecionada' => 'todas',
            'modalidadesAluno' => [],
            'aulasAluno' => [],
        ];
    }
    $id_aluno = $aluno["id_aluno"];
    $modalidadeSelecionada = $_GET['modalidade'] ?? 'todas';

    $modalidadesAluno = Modalidades::getModalidadesAgendadasByAluno($id_aluno);
    
    $aulasAluno = Aulas::getAulasByAluno($id_aluno, $modalidadeSelecionada);

    return [
        'modalidadeSelecionada' => $modalidadeSelecionada,
        'modalidadesAluno' => $modalidadesAluno,
        'aulasAluno' => $aulasAluno,
    ];
}