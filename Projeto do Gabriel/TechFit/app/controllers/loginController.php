<?php

function loginController()
{
    requireGuest();
    
    // Captura a URI para determinar se é login ou cadastro
    $uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    
    if ($uri === '/login') {
        // Verifica se é POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            processLogin();
        } else {
            // GET - exibe formulário de login
            render('loginView', 'Entrar');
        }
    } elseif ($uri === '/cadastro') {
        // Verifica se é POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            processCadastro();
        } else {
            render('cadastroView', 'Cadastre-se');
        }
    }
}

function processLogin()
{
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'] ?? '';

    // Validações básicas
    if (empty($email) || empty($senha)) {
        flash('Email e senha são obrigatórios.', 'error');
        header('Location: /login');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('Email inválido.', 'error');
        header('Location: /login');
        exit;
    }

    try {
        $usuario = Usuario::getUsuarioByEmail($email);

        if (!$usuario) {
            flash('Email ou senha incorretos.', 'error');
            header('Location: /login');
            exit;
        }

        $senhaHash = Usuario::getSenhaHash($usuario['id_usuario']);

        if (md5($senha) !== $senhaHash) {
            flash('Email ou senha incorretos.', 'error');
            header('Location: /login');
            exit;
        }

        $_SESSION['user_id'] = $usuario['id_usuario'];
        $_SESSION['user_email'] = $usuario['email'];
        $_SESSION['user_tipo'] = $usuario['tipo'];

        $usuarioCompleto = Usuario::getUsuarioCompleto($usuario['id_usuario']);
        if ($usuarioCompleto) {
            $_SESSION['user_name'] = $usuarioCompleto['user_name'];
            $_SESSION['user_avatar'] = $usuarioCompleto['user_avatar'];
            $_SESSION['user_tipo'] = $usuarioCompleto['user_tipo'];

            if ($usuarioCompleto['user_tipo'] === 'funcionario') {
                $_SESSION['id_funcionario'] = $usuarioCompleto['id_funcionario'];
            }
        }

        flash('Login realizado com sucesso!', 'success');

        header('Location: /');
        exit;
    } catch (Exception $e) {
        flash('Erro ao fazer login. Tente novamente mais tarde.', 'error');
        header('Location: /login');
        exit;
    }
}


function processCadastro()
{
    $nome = trim($_POST['nome'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $cpf = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? '');
    $data_nascimento = $_POST['data_nascimento'] ?? '';
    $genero = $_POST['genero'] ?? '';
    $telefone = preg_replace('/[^0-9]/', '', $_POST['telefone'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmarSenha = $_POST['confirmar_senha'] ?? '';
    
    $tipo = 'aluno';

    $erros = [];

    if (empty($nome)) {
        $erros[] = 'Nome é obrigatório.';
    } elseif (strlen($nome) < 3) {
        $erros[] = 'Nome deve ter pelo menos 3 caracteres.';
    } elseif (strlen($nome) > 255) {
        $erros[] = 'Nome não pode exceder 255 caracteres.';
    }

    if (empty($email)) {
        $erros[] = 'Email é obrigatório.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = 'Email inválido.';
    }

    if (empty($cpf)) {
        $erros[] = 'CPF é obrigatório.';
    } elseif (strlen($cpf) !== 11) {
        $erros[] = 'CPF deve conter 11 dígitos.';
    } elseif (!validarCPF($cpf)) {
        $erros[] = 'CPF inválido.';
    }

    if (empty($genero)) {
        $erros[] = 'Gênero é obrigatório.';
    } elseif (!in_array($genero, ['Masculino', 'Feminino', 'Outro'])) {
        $erros[] = 'Gênero inválido.';
    }

    if (empty($telefone)) {
        $erros[] = 'Telefone é obrigatório.';
    } elseif (strlen($telefone) < 10) {
        $erros[] = 'Telefone deve ter pelo menos 10 dígitos.';
    }

    if (empty($endereco)) {
        $erros[] = 'Endereço é obrigatório.';
    } elseif (strlen($endereco) < 5) {
        $erros[] = 'Endereço deve ter pelo menos 5 caracteres.';
    }

    if (empty($data_nascimento)) {
        $erros[] = 'Data de nascimento é obrigatória.';
    } else {
        $dataNasc = DateTime::createFromFormat('Y-m-d', $data_nascimento);
        if (!$dataNasc || $dataNasc->format('Y-m-d') !== $data_nascimento) {
            $erros[] = 'Data de nascimento inválida.';
        } else {
            $hoje = new DateTime();
            $idade = $hoje->diff($dataNasc)->y;
            if ($idade < 13) {
                $erros[] = 'Você deve ter pelo menos 13 anos para se cadastrar.';
            }
        }
    }

    if (empty($senha)) {
        $erros[] = 'Senha é obrigatória.';
    } elseif (strlen($senha) < 8) {
        $erros[] = 'Senha deve ter pelo menos 8 caracteres.';
    } elseif (!validarForcaSenha($senha)) {
        $erros[] = 'Senha deve conter letras maiúsculas, minúsculas e números.';
    }

    if ($senha !== $confirmarSenha) {
        $erros[] = 'As senhas não coincidem.';
    }

    if (!empty($erros)) {
        flash(implode(' ', $erros), 'error');
        header('Location: /cadastro');
        exit;
    }

    try {
        // Formata CPF para o padrão 000.000.000-00 
        $cpf_formatted = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);

        if (Usuario::emailJaExiste($email)) {
            flash('Este email já está cadastrado.', 'error');
            header('Location: /cadastro');
            exit;
        }

        if (Usuario::cpfJaExiste($cpf_formatted)) {
            flash('Este CPF já está cadastrado.', 'error');
            header('Location: /cadastro');
            exit;
        }

        $senhaHash = md5($senha);
        $idUsuario = Usuario::criar([
            'nome' => $nome,
            'email' => $email,
            'cpf' => $cpf_formatted,
            'data_nascimento' => $data_nascimento,
            'senha_hash' => $senhaHash,
            'tipo' => $tipo
        ]);

        if (!$idUsuario) {
            flash('Erro ao criar usuário. Tente novamente.', 'error');
            header('Location: /cadastro');
            exit;
        }

        $telefone_formatted = formatTelefone($telefone);

        if ($tipo === 'aluno') {
            Aluno::criarAluno([
                'id_usuario' => $idUsuario,
                'genero' => $genero,
                'endereco' => $endereco,
                'telefone' => $telefone_formatted
            ]);
        }

        flash('Cadastro realizado com sucesso! Faça login para continuar.', 'success');
        header('Location: /login');
        exit;
    } catch (Exception $e) {
        flash('Erro ao realizar cadastro. Tente novamente mais tarde.', 'error');
        header('Location: /cadastro');
        exit;
    }
}







