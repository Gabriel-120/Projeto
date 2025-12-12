<?php

require_once __DIR__ . '/agendaController.php';
require_once __DIR__ . '/configController.php';

function profileController(): void
{
    if (! isset($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit;
    }

    $usuario = Usuario::getUsuarioCompleto($_SESSION['user_id']);
    if (! $usuario) {
        flash("Usuário não encontrado", "error");
        session_destroy();
        header('Location: /');
        exit;
    }

    $currPage = $_GET['page'] ?? '';
    $action   = $_POST['action'] ?? '';

    // Dados comuns para todas as páginas do perfil
    $data = [
        'user_pfp'   => $usuario['user_avatar'],
        'user_name'  => $usuario['user_name'],
        'user_tipo'  => ucfirst($usuario['user_tipo']),
        'headExtras' => <<<HTML
            <link rel="stylesheet" href="/assets/css/profile.css" />
            <link rel="stylesheet" href="/assets/css/utility.css"/>
        HTML,
        'currPage'   =>$currPage,
    ];

    // Tratamento das ACTIONS (POST)

    if ($action) {
        switch ($action) {
            case 'cancelar':
                handleCancelarAgendamento();
                break;

            case 'update_profile':
                handleUpdateProfile($_SESSION['user_id'], $usuario['user_tipo']);
                break;

            case 'change_password':
                handleChangePassword($_SESSION['user_id']);
                break;

            case 'change_avatar':
                handleChangeAvatar($_SESSION['user_id']);
                break;
            case 'nova_avaliacao_aluno':
                handleNovaAvaliacaoAluno($_SESSION['user_id']);
                break;
            case 'novo_ticket':
                handleNovoTicket($_SESSION['user_id']);
                break;
            case 'atualizar_ticket':
                handleAtualizarTicket();
                break;
        }

        // Após qualquer ação POST, evita reprocessar ao recarregar
        if (! in_array($action, ['change_avatar', 'update_profile'])) {
            // Só redireciona se não for um dos que já redirecionam internamente
            header("Location: /profile?page=" . urlencode($currPage));
            exit;
        }
    }

    // Carregamento da sub-view e dados específicos
    switch ($currPage) {
        case 'agenda':
            $data['subView'] = 'agendaView.php';
            $data += loadAgendaData($_SESSION['user_id']);
            break;

        case 'avaliacao':
            // aluno visualiza as avaliações físicas que recebeu (não cria novas)
            $data['subView'] = 'avaliacaoView.php';

            $aluno    = Aluno::getAlunoByUserID($_SESSION['user_id']);
            $id_aluno = $aluno['id_aluno'] ?? null;

            $data['avaliacoes'] = $id_aluno ? Avaliacao::getByAluno((int) $id_aluno) : [];
            break;
        case 'suporte':
            $data['subView'] = 'suporteView.php';

            $aluno    = Aluno::getAlunoByUserID($_SESSION['user_id']);
            $id_aluno = $aluno['id_aluno'] ?? null;

            $data['tickets'] = $id_aluno ? Suporte::getByAluno((int) $id_aluno) : [];
            break;

        case 'avaliacoes_alunos':
            if ($usuario['user_tipo'] !== 'funcionario') {
                $data['subView'] = 'partials/placeholderView.php';
                $data['message'] = 'Acesso restrito aos funcionários.';
                break;
            }

            $data['subView'] = 'admin/avaliacoesAlunosView.php';

            // lista de alunos para o select
            $data['alunos'] = Aluno::getTodosComUsuario();

            $id_aluno_selecionado = isset($_GET['id_aluno']) ? (int) $_GET['id_aluno'] : 0;

            if ($id_aluno_selecionado > 0) {
                $data['id_aluno_selecionado'] = $id_aluno_selecionado;
                $data['avaliacoes']           = Avaliacao::getByAluno($id_aluno_selecionado);
            } else {
                $data['id_aluno_selecionado'] = null;
                $data['avaliacoes']           = [];
            }

            break;

        case 'frequencia':
            $data['subView'] = 'frequenciaView.php';

            // Descobre o id_aluno a partir do usuário logado
            $aluno    = Aluno::getAlunoByUserID($_SESSION['user_id']);
            $id_aluno = $aluno['id_aluno'] ?? null;

            // Lista de check-ins (pode vir vazia se nunca entrou)
            $data['frequencias'] = $id_aluno
                ? Checkin::getByAluno((int) $id_aluno)
                : [];

            break;

        case 'configuracao':
            $data['subView'] = 'configView.php';
            $data += loadConfigData($_SESSION['user_id'], $usuario['user_tipo']);
            break;

        case 'relatorios':
            handleRelatorio($usuario, $data);
            break;
        case 'suporte_admin':
            if ($usuario['user_tipo'] !== 'funcionario') {
                $data['subView'] = 'partials/placeholderView.php';
                $data['message'] = 'Acesso restrito aos funcionários.';
                break;
            }

            $data['subView'] = 'admin/suporteAdminView.php';

            $statusFiltro         = $_GET['status'] ?? 'todos';
            $data['tickets']      = Suporte::getTodos($statusFiltro);
            $data['statusFiltro'] = $statusFiltro;
            break;

        default:
            $data['subView'] = 'partials/placeholderView.php';
            $data['message'] = 'Bem-vindo à sua página de perfil!';
            break;
    }

    render('profileView', 'Perfil', $data);
}

// Funções auxiliares
function loadAgendaData(int $id_usuario): array
{
    Aulas::fecharAulasPassadas();
    $aluno                 = Aluno::getAlunoByUserID($id_usuario);
    $id_aluno              = $aluno['id_aluno'] ?? null;
    $modalidadeSelecionada = $_GET['modalidade'] ?? 'todas';

    $modalidadesAluno = $id_aluno ? Modalidades::getModalidadesAgendadasByAluno($id_aluno) : [];
    $aulasAluno       = $id_aluno ? Aulas::getAulasByAluno($id_aluno, $modalidadeSelecionada) : [];

    return [
        'modalidadeSelecionada' => $modalidadeSelecionada,
        'modalidadesAluno'      => $modalidadesAluno,
        'aulasAluno'            => $aulasAluno,
    ];
}

// Handlers
function handleCancelarAgendamento(): void
{
    $ag_id = $_POST['agendamento_id'] ?? null;
    $aluno = Aluno::getAlunoByUserID($_SESSION['user_id']);

    if (! $ag_id || ! $aluno) {
        flash("Dados inválidos para cancelamento", "error");
        return;
    }

    cancelarAgendamento((int) $ag_id, (int) $aluno['id_aluno']);
    flash("Agendamento cancelado com sucesso!", "success");
}

function handleRelatorio(array $usuario, array &$data): void
{
    if ($usuario['user_tipo'] !== 'funcionario') {
        $data['subView'] = 'partials/placeholderView.php';
        $data['message'] = 'Acesso restrito aos funcionários.';
        return;
    }

    $data['subView'] = 'admin/relatoriosView.php';

    $pdo = Connect::conectar();

    // Total de alunos
    $totalAlunos = (int) $pdo->query("SELECT COUNT(*) FROM alunos")->fetchColumn();

    // Alunos com plano ativo
    $alunosAtivos = (int) $pdo->query("
        SELECT COUNT(DISTINCT id_aluno)
        FROM planos_aluno
        WHERE status = 'ativo'
    ")->fetchColumn();

    // Receita total aprovada
    $receitaTotal = (float) $pdo->query("
        SELECT COALESCE(SUM(valor), 0)
        FROM pagamentos
        WHERE status = 'Aprovado'
    ")->fetchColumn();

    // Frequência por filial
    $stmt = $pdo->query("
        SELECT
            f.nome_filial,
            COUNT(*) AS total_checkins
        FROM checkin c
        JOIN filiais f ON c.id_filial = f.id_filial
        GROUP BY f.id_filial, f.nome_filial
        ORDER BY total_checkins DESC
    ");
    $frequenciaPorFilial = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Joga tudo para a view
    $data['totalAlunos']         = $totalAlunos;
    $data['alunosAtivos']        = $alunosAtivos;
    $data['receitaTotal']        = $receitaTotal;
    $data['frequenciaPorFilial'] = $frequenciaPorFilial;
}
function handleNovaAvaliacaoAluno(int $id_usuario): void
{
    // garante que é funcionário
    $usuario = Usuario::getUsuarioCompleto($id_usuario);
    if (! $usuario || $usuario['user_tipo'] !== 'funcionario') {
        flash("Acesso restrito aos funcionários.", "error");
        return;
    }

    $id_funcionario = $_SESSION['id_funcionario'] ?? null;
    if (! $id_funcionario) {
        flash("Funcionário não identificado.", "error");
        return;
    }

    $id_aluno    = (int) ($_POST['id_aluno'] ?? 0);
    $nota        = (float) ($_POST['nota'] ?? 0);
    $comentarios = trim($_POST['comentarios'] ?? '');

    if ($id_aluno <= 0 || $nota <= 0 || $comentarios === '') {
        flash("Preencha todos os campos da avaliação.", "error");
        return;
    }

    Avaliacao::criar($id_aluno, $id_funcionario, $nota, $comentarios);
    flash("Avaliação do aluno cadastrada com sucesso!", "success");
}

function handleNovoTicket(int $id_usuario): void
{
    $aluno = Aluno::getAlunoByUserID($id_usuario);
    if (! $aluno) {
        flash("Aluno não encontrado.", "error");
        return;
    }

    $id_aluno  = (int) $aluno['id_aluno'];
    $categoria = trim($_POST['categoria'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');

    if ($categoria === '' || $descricao === '') {
        flash("Preencha todos os campos do ticket.", "error");
        return;
    }

    $ticketID = Suporte::criar($id_aluno, $categoria, $descricao);
    flash("Ticket #$ticketID criado com sucesso!", "success");
}
function handleAtualizarTicket(): void
{
    $ticket = trim($_POST['ticket'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if ($ticket === '' || $status === '') {
        flash("Dados inválidos.", "error");
        return;
    }

    Suporte::atualizarStatus($ticket, $status);
    flash("Ticket atualizado!", "success");
}