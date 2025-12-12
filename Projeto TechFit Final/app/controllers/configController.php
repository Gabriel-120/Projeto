<?php
require_once __DIR__ . '/../helpers/validators.php';

function loadConfigData(int $id_usuario, string $tipo): array
{
    $usuario = Usuario::getUsuarioCompleto($id_usuario);

    $data = [
        'nome'  => $usuario['user_name'],
        'email' => $usuario['user_email'],
        'tipo'  => ucfirst($tipo),
    ];

    if (strtolower($tipo) === 'aluno') {
        $aluno = Aluno::getAlunoCompletoByUserID($id_usuario);
        if ($aluno) {
            $data += [
                'telefone'         => $aluno['telefone'] ?? '',
                'endereco'         => $aluno['endereco'] ?? '',
                'genero'           => $aluno['genero'] ?? '',
                'cpf'              => $aluno['cpf'] ?? '',
                'data_nascimento'  => $aluno['data_nascimento'] ?? '',
            ];
        }
    }

    return $data;
}

function handleUpdateProfile(int $id_usuario, string $tipo): void
{
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($nome === '') {
        flash("O nome é obrigatório", "error");
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash("E-mail inválido", "error");
        return;
    }

    if (Usuario::emailExists($email, $id_usuario)) {
        flash("Este e-mail já está sendo utilizado por outro usuário", "error");
        return;
    }

    Usuario::changeName($id_usuario, $nome);
    Usuario::changeEmail($id_usuario, $email);

    // Dados específicos do aluno
    if (strtolower($tipo) === 'aluno') {
        $aluno = Aluno::getAlunoByUserID($id_usuario);
        if ($aluno) {
            $alunoData = [];
            foreach (['telefone', 'endereco', 'genero'] as $campo) {
                if (isset($_POST[$campo])) {
                    $valor = trim($_POST[$campo]);
                    if ($valor !== '') {
                        $alunoData[$campo] = $valor;
                    }
                }
            }
            if (!empty($alunoData)) {
                Aluno::updateAluno($aluno['id_aluno'], $alunoData);
            }
        }
    }

    flash("Perfil atualizado com sucesso!", "success");
    header('Location: /profile?page=configuracao');
    exit;
}

function handleChangePassword(int $id_usuario): void
{
    $atual = $_POST['senha_atual'] ?? '';
    $nova  = $_POST['senha_nova'] ?? '';
    $conf  = $_POST['senha_confirmacao'] ?? '';

    if ($atual === '' || $nova === '' || $conf === '') {
        flash("Todos os campos de senha são obrigatórios", "error");
        return;
    }

    if ($nova !== $conf) {
        flash("As novas senhas não coincidem", "error");
        return;
    }

    if (strlen($nova) < 8) {
        flash("A nova senha deve ter pelo menos 8 caracteres", "error");
        return;
    }

    if (!validarForcaSenha($nova)) {
        flash("Senha deve conter letras maiúsculas, minúsculas e números", "error");
        return;
    }

    $hashArmazenado = Usuario::getSenhaHash($id_usuario);
    if (!password_verify($atual, $hashArmazenado)) {
        flash("Senha atual incorreta", "error");
        return;
    }

    $novoHash = password_hash($nova, PASSWORD_DEFAULT);
    Usuario::changePass($id_usuario, $novoHash);

    flash("Senha alterada com sucesso!", "success");
    header('Location: /profile?page=configuracao');
    exit;
}

function handleChangeAvatar(int $id_usuario): void
{
    // Mapa de erros do PHP para mensagens legíveis
    $uploadErrors = [
        UPLOAD_ERR_INI_SIZE   => "Arquivo muito grande (excede limite do servidor de " . ini_get('upload_max_filesize') . ")",
        UPLOAD_ERR_FORM_SIZE  => "Arquivo muito grande (excede limite do formulário)",
        UPLOAD_ERR_PARTIAL    => "Upload foi interrompido, tente novamente",
        UPLOAD_ERR_NO_FILE    => "Nenhum arquivo foi selecionado",
        UPLOAD_ERR_NO_TMP_DIR => "Erro temporário do servidor",
        UPLOAD_ERR_CANT_WRITE => "Erro ao salvar arquivo temporário",
        UPLOAD_ERR_EXTENSION  => "Upload bloqueado por extensão PHP",
    ];

    if (empty($_FILES['avatar'])) {
        flash("Nenhum arquivo enviado", "error");
        return;
    }

    // Verifica erros do upload
    if ($_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
        $errorCode = $_FILES['avatar']['error'];
        $errorMsg = $uploadErrors[$errorCode] ?? "Erro desconhecido no upload (código: {$errorCode})";
        
        // Log detalhado para debug
        $debugInfo = [
            'error_code' => $errorCode,
            'file_size' => $_FILES['avatar']['size'],
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_file_uploads' => ini_get('max_file_uploads'),
        ];
        error_log("Avatar upload error: " . json_encode($debugInfo));
        
        flash($errorMsg, "error");
        return;
    }

    $file = $_FILES['avatar'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    // Validação de tamanho local
    if ($file['size'] > $maxSize) {
        flash("A imagem deve ter no máximo 5MB", "error");
        return;
    }

    // Validação mais robusta de tipo MIME usando getimagesize
    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        flash("Arquivo não é uma imagem válida ou está corrompido", "error");
        return;
    }

    // Verifica se é um tipo de imagem permitido
    $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];
    if (!in_array($imageInfo[2], $allowedTypes)) {
        flash("Apenas imagens JPG, PNG ou GIF são permitidas", "error");
        return;
    }

    $uploadDir = __DIR__ . '/../../public/images/upload/pfp/';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        flash("Erro interno ao criar diretório de upload", "error");
        return;
    }

    // Remove avatar antigo (exceto o padrão)
    $usuario = Usuario::getUsuarioCompleto($id_usuario);
    $oldPath = __DIR__ . '/../../public' . $usuario['user_avatar'];
    if ($usuario['user_avatar'] !== '/images/upload/pfp/avatar.png'
        && file_exists($oldPath)
        && strpos($usuario['user_avatar'], "avatar_{$id_usuario}") !== false
    ) {
        @unlink($oldPath);
    }

    // Determina extensão pela informação da imagem
    $extension = image_type_to_extension($imageInfo[2], false);
    $newName = "avatar_{$id_usuario}." . $extension;
    $destino = $uploadDir . $newName;

    // Tenta redimensionar e cortar se a extensão GD estiver disponível
    $saveSuccess = false;
    if (extension_loaded('gd')) {
        $sourceImage = null;
        $imageType = $imageInfo[2];
        
        if ($imageType === IMAGETYPE_JPEG) {
            $sourceImage = @imagecreatefromjpeg($file['tmp_name']);
        } elseif ($imageType === IMAGETYPE_PNG) {
            $sourceImage = @imagecreatefrompng($file['tmp_name']);
        } elseif ($imageType === IMAGETYPE_GIF) {
            $sourceImage = @imagecreatefromgif($file['tmp_name']);
        }

        if ($sourceImage !== false) {
            // Calcula dimensões para corte quadrado (aspect ratio 1:1)
            $width = imagesx($sourceImage);
            $height = imagesy($sourceImage);
            $size = min($width, $height);
            $x = (int) (($width - $size) / 2);
            $y = (int) (($height - $size) / 2);

            // Cria imagem redimensionada e cortada (300x300)
            $resized = imagecreatetruecolor(300, 300);
            imagecopyresampled($resized, $sourceImage, 0, 0, $x, $y, 300, 300, $size, $size);
            imagedestroy($sourceImage);

            // Salva a imagem processada
            if ($imageType === IMAGETYPE_JPEG) {
                $saveSuccess = imagejpeg($resized, $destino, 90);
            } elseif ($imageType === IMAGETYPE_PNG) {
                $saveSuccess = imagepng($resized, $destino, 9);
            } elseif ($imageType === IMAGETYPE_GIF) {
                $saveSuccess = imagegif($resized, $destino);
            }
            imagedestroy($resized);
        }
    }

    // Se GD não estiver disponível ou falhou, copia a imagem original
    if (!$saveSuccess) {
        $saveSuccess = move_uploaded_file($file['tmp_name'], $destino);
    }

    if ($saveSuccess) {
        $url = "/images/upload/pfp/{$newName}";
        Usuario::changeAvatar($id_usuario, $url);
        flash("Avatar atualizado com sucesso!", "success");
    } else {
        flash("Falha ao salvar a imagem no servidor", "error");
    }

    header('Location: /profile?page=configuracao');
    exit;
}

    