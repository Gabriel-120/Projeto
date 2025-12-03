<?php

function comunicadoAdmController()
{
    requireFuncionario();

    $tipoLabels = getTipoLabelsAvisos();
    $erros      = [];
    $sucesso    = null;
    $old = [
        'titulo'   => '',
        'tipo'     => '',
        'conteudo' => '',
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (isAcaoDelete()) {
            $resultado = processarExclusaoAviso();
            $erros     = array_merge($erros, $resultado['erros']);
            $sucesso   = $resultado['sucesso'];

        } else {
            $input = filtrarInputComunicado();
            $old   = $input; 

            $validacao = validarComunicado($input);
            $erros     = array_merge($erros, $validacao);

            $anexoPath = null;
            if (empty($erros)) {
                $upload = processarUploadAnexo('anexo');
                $erros  = array_merge($erros, $upload['erros']);
                $anexoPath = $upload['caminho'] ?? null;
            }

            if (empty($erros)) {
                $idFuncionario = (int)$_SESSION['id_funcionario'];
                $ok = Aviso::createAviso(
                    $input['titulo'],
                    $input['tipo'],
                    $input['conteudo'],
                    $idFuncionario,
                    $anexoPath
                );

                if ($ok) {
                    $sucesso = 'Comunicado criado com sucesso.';
                    $old = [ 'titulo' => '', 'tipo' => '', 'conteudo' => '' ];
                } else {
                    $erros[] = 'Erro ao salvar o comunicado.';
                }
            }
        }
    }

    $avisos = Aviso::getAllForAdmin();

    $data = [
        'nomeTipo'   => $tipoLabels,
        'avisoTipos' => Aviso::getTipos(),
        'erros'      => $erros,
        'sucesso'    => $sucesso,
        'old'        => $old,
        'avisos'     => $avisos,
    ];

    render("admin/comunicadoView", "Gerenciar comunicados", $data);
}


function getTipoLabelsAvisos(): array
{
    return [
        'Comunicado'      => 'Comunicado',
        'Promocao'        => 'Promoção',
        'Evento'          => 'Evento',
        'Manutencao'      => 'Manutenção',
        'MudancaHorario'  => 'Mudança de Horário',
        'Novidade'        => 'Novidade',
        'DicaSaude'       => 'Dicas de Saúde',
        'AvisoSeguranca'  => 'Aviso de Segurança',
    ];
}

/**
 * Verifica se a ação do POST é exclusão
 */
function isAcaoDelete(): bool
{
    return isset($_POST['acao']) && $_POST['acao'] === 'delete';
}

/**
 * Lê e limpa os dados do formulário de comunicado
 */
function filtrarInputComunicado(): array
{
    return [
        'titulo'   => trim($_POST['titulo']   ?? ''),
        'tipo'     => trim($_POST['tipo']     ?? ''),
        'conteudo' => trim($_POST['conteudo'] ?? ''),
    ];
}

/**
 * Valida os dados do comunicado
 */
function validarComunicado(array $input): array
{
    $erros = [];

    if ($input['titulo'] === '') {
        $erros[] = 'O título é obrigatório.';
    }

    if ($input['conteudo'] === '') {
        $erros[] = 'O conteúdo é obrigatório.';
    }

    $tiposValidos = array_keys(getTipoLabelsAvisos());
    if (!in_array($input['tipo'], $tiposValidos, true)) {
    $erros[] = 'Tipo de aviso inválido.';
    }

    return $erros;
}

/**
 * Processa a exclusão de um aviso
 */
function processarExclusaoAviso(): array
{
    $erros   = [];
    $sucesso = null;

    $id_alerta = (int)($_POST['id_alerta'] ?? 0);

    if ($id_alerta <= 0) {
        $erros[] = 'ID de comunicado inválido para exclusão.';
    } else {
        $ok = Aviso::deleteAviso($id_alerta);
        if ($ok) {
            $sucesso = 'Comunicado excluído com sucesso.';
        } else {
            $erros[] = 'Erro ao excluir o comunicado.';
        }
    }

    return compact('erros', 'sucesso');
}




function processarUploadAnexo(string $campo): array
{
    $erros  = [];
    $caminho = null;

    if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) {
        return compact('erros', 'caminho'); // anexo opcional
    }

    $file = $_FILES[$campo];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $erros[] = 'Erro no upload do arquivo (código: ' . $file['error'] . ').';
        return compact('erros', 'caminho');
    }

    // validação simples de tipo e tamanho
    $maxBytes = 2 * 1024 * 1024; // 2MB
    if ($file['size'] > $maxBytes) {
        $erros[] = 'Arquivo muito grande (máx. 2MB).';
        return compact('erros', 'caminho');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];

    if (!isset($permitidos[$mime])) {
        $erros[] = 'Tipo de arquivo não permitido. Envie uma imagem (jpg, png, gif).';
        return compact('erros', 'caminho');
    }

    $ext      = $permitidos[$mime];
    $uploadDirFs     = __DIR__ . '/../../public/images/upload/avisos/';
    $uploadDirPublic = '/images/upload/avisos/';

    if (!is_dir($uploadDirFs)) {
        mkdir($uploadDirFs, 0777, true);
    }

    $nomeFinal = uniqid('aviso_', true) . '.' . $ext;

    $destinoFs     = $uploadDirFs . $nomeFinal;
    $destinoPublic = $uploadDirPublic . $nomeFinal;

    if (!move_uploaded_file($file['tmp_name'], $destinoFs)) {
        $erros[] = 'Falha ao salvar o arquivo de anexo.';
        return compact('erros', 'caminho');
    }

    $caminho = $destinoPublic;
    return compact('erros', 'caminho');
}
