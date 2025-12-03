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

    switch ($act){
        case 'cancelar':
            $ag_id = $_POST['agendamento_id'];
           
            $aluno = Aluno::getAlunoByUserID($_SESSION["user_id"]);
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