<?php

function comunicadoPublicController()
{
    Aviso::deleteExpired();

    $erros   = [];
    $sucesso = null;

    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $avisoSelecionado = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (($_SESSION['user_tipo'] ?? null) !== 'funcionario') {
            http_response_code(403);
            echo "Acesso negado.";
            exit;
        }

        $acao = $_POST['acao'] ?? '';

        if ($acao === 'delete') {
            $id_alerta = (int)($_POST['id_alerta'] ?? 0);

            if ($id_alerta <= 0) {
                $erros[] = 'ID inválido para exclusão.';
            } else {
                $aviso = Aviso::getById($id_alerta);

                if (!empty($aviso['anexo_path'])) {
                    $arquivoFisico = __DIR__ . '/../../public' . $aviso['anexo_path'];
                    if (file_exists($arquivoFisico)) {
                        unlink($arquivoFisico);
                    }
                }

                if (Aviso::deleteAviso($id_alerta)) {
                    header('Location: /comunicados');
                    exit;
                } else {
                    $erros[] = 'Erro ao excluir o comunicado.';
                }
            }

        } elseif ($acao === 'edit') {
            $id_alerta = (int)($_POST['id_alerta'] ?? 0);
            $titulo    = trim($_POST['titulo'] ?? '');
            $tipo      = trim($_POST['tipo'] ?? '');
            $conteudo  = trim($_POST['conteudo'] ?? '');
            $expira    = trim($_POST['expira'] ?? '');

            if ($id_alerta <= 0) $erros[] = 'ID inválido para edição.';
            if ($titulo === '') $erros[] = 'O título é obrigatório.';
            if ($conteudo === '') $erros[] = 'O conteúdo é obrigatório.';

            $tiposValidos = array_keys(Aviso::getTipoLabelsAvisos());
            if (!in_array($tipo, $tiposValidos, true)) {
                $erros[] = 'Tipo de aviso inválido.';
            }
            if ($expira === '') $erros[] = 'A data de expiração é obrigatória.';

            $novoAnexoPath = null;

            if (!empty($_FILES['anexo']['name'])) {
                $arquivoTmp = $_FILES['anexo']['tmp_name'];
                $arquivoNome = "aviso_" . uniqid() . "_" . basename($_FILES['anexo']['name']);
                
                $uploadDir = __DIR__ . '/../../public/images/upload/avisos/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $destino = $uploadDir . $arquivoNome;

                if (move_uploaded_file($arquivoTmp, $destino)) {
                    $novoAnexoPath = "/images/upload/avisos/" . $arquivoNome;

                    $avisoAtual = Aviso::getById($id_alerta);
                    if (!empty($avisoAtual['anexo_path'])) {
                        $arquivoAntigo = __DIR__ . '/../../public' . $avisoAtual['anexo_path'];
                        if (file_exists($arquivoAntigo)) {
                            unlink($arquivoAntigo);
                        }
                    }
                } else {
                    $erros[] = 'Erro ao enviar o novo anexo.';
                }
            }

            if (empty($erros)) {
                if (Aviso::updateAviso($id_alerta, $titulo, $tipo, $conteudo, $expira, $novoAnexoPath)) {
                    $sucesso = 'Comunicado atualizado com sucesso.';
                    $id = $id_alerta;
                } else {
                    $erros[] = 'Erro ao atualizar o comunicado.';
                }
            }

            if ($id_alerta > 0) {
                $avisoSelecionado = Aviso::getById($id_alerta);
            }
        }
    }

    if ($avisoSelecionado === null && $id > 0) {
        $avisoSelecionado = Aviso::getById($id);
    }

    $avisos = Aviso::getAllForAdmin();
    $tipoLabels = Aviso::getTipoLabelsAvisos();

    $data = [
        'avisos'           => $avisos,
        'avisoSelecionado' => $avisoSelecionado,
        'nomeTipo'         => $tipoLabels,
        'erros'            => $erros,
        'sucesso'          => $sucesso,
        'isFuncionario'    => ($_SESSION['user_tipo'] ?? null) === 'funcionario',
    ];

    render("comunicados/feedView", "Comunicados", $data);
}
