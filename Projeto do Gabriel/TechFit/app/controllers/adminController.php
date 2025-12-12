<?php

require_once __DIR__ . '/../models/Planos.php';

function adminController(): void
{
    requireFuncionario();

    $action = $_POST['action'] ?? '';
    $tab    = $_GET['active_tab'] ?? 'usuarios';

    // CRUD simples de usuários
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        switch ($action) {
            case 'add_user':
                $tab             = 'usuarios';
                $nome            = trim($_POST['nome'] ?? '');
                $email           = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
                $cpf             = preg_replace('/[^0-9]/', '', $_POST['cpf'] ?? '');
                $data_nascimento = $_POST['data_nascimento'] ?? '';
                $tipo            = trim($_POST['tipo'] ?? 'aluno');
                $senha           = $_POST['senha'] ?? '';

                // Campos específicos por tipo
                $enderecoAluno    = trim($_POST['endereco_aluno'] ?? '');
                $generoAluno      = trim($_POST['genero_aluno'] ?? '');
                $telefoneAluno    = trim($_POST['telefone_aluno'] ?? '');
                $salarioFunc      = isset($_POST['salario_func']) ? (float) $_POST['salario_func'] : 0;
                $cargaHorariaFunc = isset($_POST['carga_horaria_func']) ? (int) $_POST['carga_horaria_func'] : 0;
                $cargoFunc        = trim($_POST['cargo_func'] ?? '');

                // Validações reutilizando a lógica do cadastro
                $erros = [];

                if (empty($nome)) {
                    $erros[] = 'Nome é obrigatório.';
                } elseif (strlen($nome) < 3) {
                    $erros[] = 'Nome deve ter pelo menos 3 caracteres.';
                }

                if (empty($email)) {
                    $erros[] = 'Email é obrigatório.';
                } elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $erros[] = 'Email inválido.';
                }

                if (empty($cpf)) {
                    $erros[] = 'CPF é obrigatório.';
                } elseif (strlen($cpf) !== 11) {
                    $erros[] = 'CPF deve conter 11 dígitos.';
                } elseif (! validarCPF($cpf)) {
                    $erros[] = 'CPF inválido.';
                }

                if (empty($senha)) {
                    $erros[] = 'Senha é obrigatória.';
                } elseif (strlen($senha) < 8) {
                    $erros[] = 'Senha deve ter pelo menos 8 caracteres.';
                }

                if (empty($data_nascimento)) {
                    $erros[] = 'Data de nascimento é obrigatória.';
                } else {
                    $dataNasc = DateTime::createFromFormat('Y-m-d', $data_nascimento);
                    if (! $dataNasc || $dataNasc->format('Y-m-d') !== $data_nascimento) {
                        $erros[] = 'Data de nascimento inválida.';
                    } else {
                        $hoje  = new DateTime();
                        $idade = $hoje->diff($dataNasc)->y;
                        if ($idade < 13) {
                            $erros[] = 'Usuário deve ter pelo menos 13 anos.';
                        }
                    }
                }

                // Verificações específicas por tipo
                if ($tipo === 'aluno') {
                    if (empty($generoAluno)) {
                        $erros[] = 'Gênero do aluno é obrigatório.';
                    }
                    if (empty($enderecoAluno)) {
                        $erros[] = 'Endereço do aluno é obrigatório.';
                    }
                    if (empty($telefoneAluno)) {
                        $erros[] = 'Telefone do aluno é obrigatório.';
                    }
                } elseif ($tipo === 'funcionario') {
                    if ($salarioFunc <= 0) {
                        $erros[] = 'Salário do funcionário deve ser maior que zero.';
                    }
                    if ($cargaHorariaFunc <= 0) {
                        $erros[] = 'Carga horária do funcionário deve ser maior que zero.';
                    }
                    if (empty($cargoFunc)) {
                        $erros[] = 'Cargo do funcionário é obrigatório.';
                    }
                }

                // Verificar se email ou CPF já existem
                if (Usuario::emailJaExiste($email)) {
                    $erros[] = 'Este email já está cadastrado.';
                }

                $cpf_formatted = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
                if (Usuario::cpfJaExiste($cpf_formatted)) {
                    $erros[] = 'Este CPF já está cadastrado.';
                }

                if (! empty($erros)) {
                    foreach ($erros as $erro) {
                        flash($erro, 'error');
                    }
                } else {
                    try {
                        $pdo = Connect::conectar();
                        $pdo->beginTransaction();

                        $senhaHash = md5($senha);

                        // Cria usuário principal
                        $idUsuario = Usuario::criar([
                            'nome'            => $nome,
                            'email'           => $email,
                            'cpf'             => $cpf_formatted,
                            'data_nascimento' => $data_nascimento,
                            'senha_hash'      => $senhaHash,
                            'tipo'            => $tipo,
                        ]);

                        // Cria registro complementar conforme o tipo
                        if ($tipo === 'aluno') {
                            // Cria aluno vinculado ao usuário com todos os campos necessários
                            Aluno::criarAluno([
                                'id_usuario' => $idUsuario,
                                'genero'     => $generoAluno,
                                'endereco'   => $enderecoAluno,
                                'telefone'   => $telefoneAluno,
                            ]);
                        } elseif ($tipo === 'funcionario') {
                            // Cria funcionário com dados básicos vindos do formulário
                            $stmtFunc = $pdo->prepare('INSERT INTO Funcionarios (salario, carga_horaria, cargo, id_usuario) VALUES (:salario, :carga, :cargo, :id_usuario)');
                            $stmtFunc->execute([
                                ':salario'    => $salarioFunc > 0 ? $salarioFunc : 0,
                                ':carga'      => $cargaHorariaFunc > 0 ? $cargaHorariaFunc : 0,
                                ':cargo'      => $cargoFunc !== '' ? $cargoFunc : 'Pendente',
                                ':id_usuario' => $idUsuario,
                            ]);
                        }

                        $pdo->commit();
                        flash('Usuário criado com sucesso.', 'success');
                    } catch (Exception $e) {
                        if (isset($pdo) && $pdo->inTransaction()) {
                            $pdo->rollBack();
                        }
                        flash('Erro ao criar usuário: ' . $e->getMessage(), 'error');
                    }
                }
                break;

            // --- Planos ---
            case 'add_plano':
                $tab       = 'planos';
                $nome      = trim($_POST['nome_plano'] ?? '');
                $descricao = trim($_POST['descricao_plano'] ?? '');
                $preco     = floatval($_POST['preco'] ?? 0);
                $duracao   = intval($_POST['duracao'] ?? 30);
                if ($nome === '' || $preco <= 0) {
                    flash('Dados inválidos para plano', 'error');
                } else {
                    if (Planos::create($nome, $descricao, $preco, $duracao)) {
                        flash('Plano criado.', 'success');
                    } else {
                        flash('Erro ao criar plano', 'error');
                    }
                }
                break;

            // --- Modalidades ---
            case 'add_modalidade':
                $tab       = 'modalidades';
                $nome      = trim($_POST['nome_modalidade'] ?? '');
                $descricao = trim($_POST['descricao_modalidade'] ?? '');
                if ($nome === '') {
                    flash('Nome da modalidade obrigatório', 'error');
                } else {
                    $pdo  = Connect::conectar();
                    $stmt = $pdo->prepare('INSERT INTO Modalidades (nome_modalidade, descricao) VALUES (:nome, :descricao)');
                    $ok   = $stmt->execute([':nome' => $nome, ':descricao' => $descricao]);
                    if ($ok) {
                        flash('Modalidade criada.', 'success');
                    } else {
                        flash('Erro ao criar modalidade', 'error');
                    }
                }
                break;

            case 'edit_modalidade':
                $tab       = 'modalidades';
                $id        = intval($_POST['id_modalidade'] ?? 0);
                $nome      = trim($_POST['nome_modalidade'] ?? '');
                $descricao = trim($_POST['descricao_modalidade'] ?? '');
                if ($id <= 0 || $nome === '') {
                    flash('Dados inválidos para modalidade', 'error');
                } else {
                    $pdo  = Connect::conectar();
                    $stmt = $pdo->prepare('UPDATE Modalidades SET nome_modalidade = :nome, descricao = :descricao WHERE id_modalidade = :id');
                    $ok   = $stmt->execute([':nome' => $nome, ':descricao' => $descricao, ':id' => $id]);
                    if ($ok) {
                        flash('Modalidade atualizada.', 'success');
                    } else {
                        flash('Erro ao atualizar modalidade', 'error');
                    }
                }
                break;

            case 'delete_modalidade':
                $tab = 'modalidades';
                $id  = intval($_POST['id_modalidade'] ?? 0);
                if ($id <= 0) {
                    flash('ID inválido', 'error');
                } else {
                    $ok = Connect::conectar()->prepare('DELETE FROM Modalidades WHERE id_modalidade = :id')->execute([':id' => $id]);
                    if ($ok) {
                        flash('Modalidade removida.', 'success');
                    } else {
                        flash('Erro ao remover modalidade', 'error');
                    }
                }
                break;

            // --- Filiais ---
            case 'add_filial':
                $tab  = 'filiais';
                $nome = trim($_POST['nome_filial'] ?? '');
                $end  = trim($_POST['endereco_filial'] ?? '');
                $tel  = trim($_POST['telefone_filial'] ?? '');
                if ($nome === '') {
                    flash('Nome da filial obrigatório', 'error');
                } else {
                    $pdo  = Connect::conectar();
                    $stmt = $pdo->prepare('INSERT INTO Filiais (nome_filial, endereco, telefone) VALUES (:nome, :endereco, :telefone)');
                    $ok   = $stmt->execute([':nome' => $nome, ':endereco' => $end, ':telefone' => $tel]);
                    if ($ok) {
                        flash('Filial criada.', 'success');
                    } else {
                        flash('Erro ao criar filial', 'error');
                    }
                }
                break;

            case 'edit_filial':
                $tab  = 'filiais';
                $id   = intval($_POST['id_filial'] ?? 0);
                $nome = trim($_POST['nome_filial'] ?? '');
                $end  = trim($_POST['endereco_filial'] ?? '');
                $tel  = trim($_POST['telefone_filial'] ?? '');
                if ($id <= 0 || $nome === '') {
                    flash('Dados inválidos para filial', 'error');
                } else {
                    $pdo  = Connect::conectar();
                    $stmt = $pdo->prepare('UPDATE Filiais SET nome_filial = :nome, endereco = :endereco, telefone = :telefone WHERE id_filial = :id');
                    $ok   = $stmt->execute([':nome' => $nome, ':endereco' => $end, ':telefone' => $tel, ':id' => $id]);
                    if ($ok) {
                        flash('Filial atualizada.', 'success');
                    } else {
                        flash('Erro ao atualizar filial', 'error');
                    }
                }
                break;

            case 'delete_filial':
                $tab = 'filiais';
                $id  = intval($_POST['id_filial'] ?? 0);
                if ($id <= 0) {
                    flash('ID inválido', 'error');
                } else {
                    $ok = Connect::conectar()->prepare('DELETE FROM Filiais WHERE id_filial = :id')->execute([':id' => $id]);
                    if ($ok) {
                        flash('Filial removida.', 'success');
                    } else {
                        flash('Erro ao remover filial', 'error');
                    }
                }
                break;

            case 'delete_plano':
                $tab = 'planos';
                $id  = intval($_POST['id_plano'] ?? 0);
                if ($id <= 0) {
                    flash('ID inválido', 'error');
                } else {
                    if (Planos::delete($id)) {
                        flash('Plano removido.', 'success');
                    } else {
                        flash('Erro ao remover plano', 'error');
                    }
                }
                break;

            case 'edit_plano':
                $tab       = 'planos';
                $id        = intval($_POST['id_plano'] ?? 0);
                $nome      = trim($_POST['nome_plano'] ?? '');
                $descricao = trim($_POST['descricao_plano'] ?? '');
                $preco     = floatval($_POST['preco'] ?? 0);
                $duracao   = intval($_POST['duracao'] ?? 0);
                if ($id <= 0 || $nome === '') {
                    flash('Dados inválidos', 'error');
                } else {
                    if (Planos::update($id, $nome, $descricao, $preco, $duracao)) {
                        flash('Plano atualizado.', 'success');
                    } else {
                        flash('Erro ao atualizar plano', 'error');
                    }
                }
                break;

            // --- Aulas ---
            case 'add_aula':
                $tab       = 'aulas';
                $nomeA     = trim($_POST['nome_aula'] ?? '');
                $dia       = trim($_POST['dia_aula'] ?? '');
                $qtd       = intval($_POST['quantidade'] ?? 0);
                $descricao = trim($_POST['descricao_aula'] ?? '');
                $id_func   = intval($_POST['id_funcionario_aula'] ?? 0);
                $id_modal  = intval($_POST['id_modalidade_aula'] ?? 0);
                $id_filial = intval($_POST['id_filial_aula'] ?? 0);
                if ($nomeA === '' || $dia === '') {
                    flash('Dados inválidos para aula', 'error');
                } else {
                    $pdo  = Connect::conectar();
                    $stmt = $pdo->prepare('INSERT INTO Aulas (nome_aula, dia_aula, quantidade_pessoas, descricao, id_funcionario, id_modalidade, id_filial) VALUES (:nome,:dia,:qtd,:desc,:idf,:idm,:idfili)');
                    $ok   = $stmt->execute([':nome' => $nomeA, ':dia' => $dia, ':qtd' => $qtd, ':desc' => $descricao, ':idf' => $id_func, ':idm' => $id_modal, ':idfili' => $id_filial]);
                    if ($ok) {
                        flash('Aula criada.', 'success');
                    } else {
                        flash('Erro ao criar aula', 'error');
                    }
                }
                break;

            case 'delete_aula':
                $tab = 'aulas';
                $id  = intval($_POST['id_aula'] ?? 0);
                if ($id <= 0) {
                    flash('ID inválido', 'error');
                } else {
                    $ok = Connect::conectar()->prepare('DELETE FROM Aulas WHERE id_aula = :id')->execute([':id' => $id]);
                    if ($ok) {
                        flash('Aula removida.', 'success');
                    } else {
                        flash('Erro ao remover aula', 'error');
                    }
                }
                break;

            case 'delete_user':
                $tab = 'usuarios';
                $id  = (int) ($_POST['id_usuario'] ?? 0);
                if ($id <= 0) {
                    flash('ID inválido.', 'error');
                } else {
                    $pdo  = Connect::conectar();
                    $stmt = $pdo->prepare('DELETE FROM Usuarios WHERE id_usuario = :id');
                    $ok   = $stmt->execute([':id' => $id]);
                    if ($ok) {
                        flash('Usuário removido.', 'success');
                    } else {
                        flash('Erro ao remover.', 'error');
                    }
                }
                break;

            case 'edit_user':
                $tab       = 'usuarios';
                $id        = (int) ($_POST['id_usuario'] ?? 0);
                $nome      = trim($_POST['nome'] ?? '');
                $email     = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
                $novaSenha = trim($_POST['senha_hash'] ?? '');
                $pass      = '';
                if ($novaSenha !== '') {
                    $pass = md5($novaSenha);
                }
                $tipo = trim($_POST['tipo'] ?? 'aluno');

                // Campos específicos recebidos do form de edição
                $enderecoAlunoEdit = trim(string: $_POST['edit_endereco_aluno'] ?? '');
                $generoAlunoEdit   = trim($_POST['edit_genero_aluno'] ?? '');
                $telefoneAlunoEdit = trim($_POST['edit_telefone_aluno'] ?? '');
                $salarioFunc       = isset($_POST['edit_salario_func']) ? (float) $_POST['edit_salario_func'] : 0;
                $cargaHorariaFunc  = isset($_POST['edit_carga_horaria_func']) ? (int) $_POST['edit_carga_horaria_func'] : 0;
                $cargoFunc         = trim($_POST['edit_cargo_func'] ?? '');

                if ($id <= 0 || $nome === '' || $email === '') {
                    flash('Dados inválidos.', 'error');
                } elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    flash('Email inválido.', 'error');
                } elseif (Usuario::emailExists($email, $id)) {
                    flash('Este email já está em uso por outro usuário.', 'error');
                } else {
                    $pdo = Connect::conectar();

                    try {
                        $pdo->beginTransaction();

                        // Descobre o tipo atual do usuário antes da alteração
                        $stmtTipo = $pdo->prepare('SELECT tipo FROM Usuarios WHERE id_usuario = :id');
                        $stmtTipo->execute([':id' => $id]);
                        $tipoAtual = $stmtTipo->fetchColumn();

                        // Atualiza dados básicos do usuário (incluindo novo tipo)
                        $sql    = 'UPDATE Usuarios SET nome = :nome, email = :email, tipo = :tipo ';
                        $params = [
                            ':nome'  => $nome,
                            ':email' => $email,
                            ':tipo'  => $tipo,
                            ':id'    => $id,
                        ];

                        if (! empty($novaSenha)) {
                            $sql .= ', senha_hash = :senha_hash';
                            $params[':senha_hash'] = md5($novaSenha);
                        }

                        $sql .= ' WHERE id_usuario = :id';
                        $stmt = $pdo->prepare($sql);

                        $stmt->execute($params);

                        $tipoAlterado = $tipoAtual !== false && $tipoAtual !== $tipo;

                        if ($tipoAlterado) {
                            // Se mudou de aluno para funcionário
                            if ($tipoAtual === 'aluno' && $tipo === 'funcionario') {
                                // Apaga registro de aluno antigo
                                $stmtDelAluno = $pdo->prepare('DELETE FROM Alunos WHERE id_usuario = :id_usuario');
                                $stmtDelAluno->execute([':id_usuario' => $id]);

                                // Cria novo registro em Funcionarios
                                $stmtFunc = $pdo->prepare('INSERT INTO Funcionarios (salario, carga_horaria, cargo, id_usuario) VALUES (:salario, :carga, :cargo, :id_usuario)');
                                $stmtFunc->execute([
                                    ':salario'    => $salarioFunc,
                                    ':carga'      => $cargaHorariaFunc,
                                    ':cargo'      => $cargoFunc !== '' ? $cargoFunc : 'Pendente',
                                    ':id_usuario' => $id,
                                ]);
                            }
                            // Se mudou de funcionario para aluno
                            elseif ($tipoAtual === 'funcionario' && $tipo === 'aluno') {
                                // Apaga registro de funcionário antigo
                                $stmtDelFunc = $pdo->prepare('DELETE FROM Funcionarios WHERE id_usuario = :id_usuario');
                                $stmtDelFunc->execute([':id_usuario' => $id]);

                                // Cria novo registro em Alunos com os dados informados
                                $stmtAluno = $pdo->prepare('INSERT INTO Alunos (genero, endereco, telefone, codigo_acesso, id_usuario) VALUES (:genero, :endereco, :telefone, NULL, :id_usuario)');
                                $stmtAluno->execute([
                                    ':genero'     => $generoAlunoEdit !== '' ? $generoAlunoEdit : 'N/D',
                                    ':endereco'   => $enderecoAlunoEdit,
                                    ':telefone'   => $telefoneAlunoEdit,
                                    ':id_usuario' => $id,
                                ]);
                            }
                        } else {
                            if ($tipo === 'aluno') {
                                if ($enderecoAlunoEdit !== '' || $generoAlunoEdit !== '' || $telefoneAlunoEdit !== '') {
                                    // Verifica se já existe registro de aluno para este usuário
                                    $stmtCheck = $pdo->prepare('SELECT id_aluno FROM Alunos WHERE id_usuario = :id_usuario');
                                    $stmtCheck->execute([':id_usuario' => $id]);
                                    $existeAluno = $stmtCheck->fetchColumn();

                                    if ($existeAluno) {
                                        // Atualiza dados do aluno existente
                                        $stmtAluno = $pdo->prepare('UPDATE Alunos SET genero = :genero, endereco = :endereco, telefone = :telefone WHERE id_usuario = :id_usuario');
                                        $stmtAluno->execute([
                                            ':genero'     => $generoAlunoEdit !== '' ? $generoAlunoEdit : 'N/D',
                                            ':endereco'   => $enderecoAlunoEdit,
                                            ':telefone'   => $telefoneAlunoEdit,
                                            ':id_usuario' => $id,
                                        ]);
                                    } else {
                                        // Cria registro de aluno se ainda não existir (evita duplicar para o mesmo usuário)
                                        $stmtAluno = $pdo->prepare('INSERT INTO Alunos (genero, endereco, telefone, codigo_acesso, id_usuario) VALUES (:genero, :endereco, :telefone, NULL, :id_usuario)');
                                        $stmtAluno->execute([
                                            ':genero'     => $generoAlunoEdit !== '' ? $generoAlunoEdit : 'N/D',
                                            ':endereco'   => $enderecoAlunoEdit,
                                            ':telefone'   => $telefoneAlunoEdit,
                                            ':id_usuario' => $id,
                                        ]);
                                    }
                                }
                            } elseif ($tipo === 'funcionario') {
                                $stmtCheck = $pdo->prepare('SELECT id_funcionario FROM Funcionarios WHERE id_usuario = :id_usuario');
                                $stmtCheck->execute([':id_usuario' => $id]);
                                $existeFuncionario = $stmtCheck->fetchColumn();

                                if ($existeFuncionario) {
                                    // UPDATE
                                    $stmtFunc = $pdo->prepare('UPDATE Funcionarios
                               SET salario = :salario, carga_horaria = :carga, cargo = :cargo
                               WHERE id_usuario = :id_usuario');
                                    $stmtFunc->execute([
                                        ':salario'    => $salarioFunc,
                                        ':carga'      => $cargaHorariaFunc,
                                        ':cargo'      => $cargoFunc ?: 'Pendente',
                                        ':id_usuario' => $id,
                                    ]);
                                } else {
                                    // INSERT
                                    $stmtFunc = $pdo->prepare('INSERT INTO Funcionarios
                               (salario, carga_horaria, cargo, id_usuario)
                               VALUES (:salario, :carga, :cargo, :id_usuario)');
                                    $stmtFunc->execute([
                                        ':salario'    => $salarioFunc,
                                        ':carga'      => $cargaHorariaFunc,
                                        ':cargo'      => $cargoFunc ?: 'Pendente',
                                        ':id_usuario' => $id,
                                    ]);
                                }

                            }
                        }

                        $pdo->commit();
                        flash('Usuário atualizado.', 'success');
                    } catch (Exception $e) {
                        if ($pdo->inTransaction()) {
                            $pdo->rollBack();
                        }
                        flash('Erro ao atualizar: ' . $e->getMessage(), 'error');
                    }
                }
                break;

        }

        if (! headers_sent()) {
            $redirect = '/admin/painel' . (! empty($tab) ? '?active_tab=' . urlencode($tab) : '');
            header('Location: ' . $redirect);
            exit;
        }
    }

    // buscar lista de usuários (com filtros opcionais por nome e tipo)
    $pdo = Connect::conectar();

    // Filtros de usuários
    $nomeFiltro = trim($_GET['f_nome'] ?? '');
    $tipoFiltro = trim($_GET['f_tipo'] ?? '');

    $sqlUsuarios = 'SELECT u.id_usuario, u.nome, u.email, u.cpf, u.tipo, u.senha_hash,
                              a.genero        AS genero_aluno,
                              a.endereco      AS endereco_aluno,
                              a.telefone      AS telefone_aluno,
                              f.salario       AS salario_func,
                              f.carga_horaria AS carga_horaria_func,
                              f.cargo         AS cargo_func
                       FROM Usuarios u
                       LEFT JOIN Alunos a ON a.id_usuario = u.id_usuario
                       LEFT JOIN Funcionarios f ON f.id_usuario = u.id_usuario
                       WHERE 1=1';
    $paramsUsuarios = [];

    if ($nomeFiltro !== '') {
        $sqlUsuarios .= ' AND u.nome LIKE :nome';
        $paramsUsuarios[':nome'] = '%' . $nomeFiltro . '%';
    }

    if ($tipoFiltro !== '') {
        $sqlUsuarios .= ' AND u.tipo = :tipo';
        $paramsUsuarios[':tipo'] = $tipoFiltro;
    }

    $sqlUsuarios .= ' ORDER BY u.id_usuario DESC';

    $stmt = $pdo->prepare($sqlUsuarios);
    $stmt->execute($paramsUsuarios);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // buscar funcionarios (para seleção de professor nas aulas)
    $stmtF = $pdo->query('SELECT f.id_funcionario, f.salario, f.carga_horaria, f.cargo, f.id_usuario, u.nome
                          FROM Funcionarios f
                          JOIN Usuarios u ON f.id_usuario = u.id_usuario
                          ORDER BY u.nome');
    $funcionarios = $stmtF->fetchAll(PDO::FETCH_ASSOC);

    // Filtros de planos, modalidades, aulas e filiais
    $planoNomeFiltro      = trim($_GET['f_plano_nome'] ?? '');
    $modalidadeNomeFiltro = trim($_GET['f_modalidade_nome'] ?? '');
    $aulaNomeFiltro       = trim($_GET['f_aula_nome'] ?? '');
    $filialNomeFiltro     = trim($_GET['f_filial_nome'] ?? '');

    // Planos
    $sqlPlanos = 'SELECT id_plano, nome_plano, descricao_plano, preco, duracao FROM Planos WHERE 1=1';
    $paramsPlanos = [];
    if ($planoNomeFiltro !== '') {
        $sqlPlanos .= ' AND nome_plano LIKE :nome_plano';
        $paramsPlanos[':nome_plano'] = '%' . $planoNomeFiltro . '%';
    }
    $sqlPlanos .= ' ORDER BY id_plano DESC';
    $stmtPlanos = $pdo->prepare($sqlPlanos);
    $stmtPlanos->execute($paramsPlanos);
    $planos = $stmtPlanos->fetchAll(PDO::FETCH_ASSOC);

    // Aulas
    $sqlAulas = 'SELECT A.*, M.nome_modalidade, F.nome_filial
                 FROM Aulas A
                 LEFT JOIN Modalidades M ON A.id_modalidade = M.id_modalidade
                 LEFT JOIN Filiais F ON A.id_filial = F.id_filial
                 WHERE 1=1';
    $paramsAulas = [];
    if ($aulaNomeFiltro !== '') {
        $sqlAulas .= ' AND A.nome_aula LIKE :nome_aula';
        $paramsAulas[':nome_aula'] = '%' . $aulaNomeFiltro . '%';
    }
    $sqlAulas .= ' ORDER BY A.dia_aula DESC';
    $stmtA  = $pdo->prepare($sqlAulas);
    $stmtA->execute($paramsAulas);
    $aulas  = $stmtA->fetchAll(PDO::FETCH_ASSOC);

    // Modalidades
    $sqlModalidades = 'SELECT id_modalidade, nome_modalidade, descricao FROM Modalidades WHERE 1=1';
    $paramsModalidades = [];
    if ($modalidadeNomeFiltro !== '') {
        $sqlModalidades .= ' AND nome_modalidade LIKE :nome_modalidade';
        $paramsModalidades[':nome_modalidade'] = '%' . $modalidadeNomeFiltro . '%';
    }
    $sqlModalidades .= ' ORDER BY nome_modalidade';
    $stmtM = $pdo->prepare($sqlModalidades);
    $stmtM->execute($paramsModalidades);
    $modalidades = $stmtM->fetchAll(PDO::FETCH_ASSOC);

    // Filiais
    $filiais = [];
    try {
        $sqlFiliais = 'SELECT id_filial, nome_filial, endereco, telefone FROM Filiais WHERE 1=1';
        $paramsFiliais = [];
        if ($filialNomeFiltro !== '') {
            $sqlFiliais .= ' AND nome_filial LIKE :nome_filial';
            $paramsFiliais[':nome_filial'] = '%' . $filialNomeFiltro . '%';
        }
        $sqlFiliais .= ' ORDER BY id_filial DESC';

        $stmtF   = $pdo->prepare($sqlFiliais);
        $stmtF->execute($paramsFiliais);
        $filiais = $stmtF->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        
    }

    $data = [
        'usuarios'            => $usuarios,
        'funcionarios'        => $funcionarios,
        'planos'              => $planos,
        'aulas'               => $aulas,
        'modalidades'         => $modalidades,
        'filiais'             => $filiais,
        'avisos'              => method_exists('Aviso', 'getAllForAdmin') ? Aviso::getAllForAdmin() : [],
        'nomeTipo'            => method_exists('Aviso', 'getTipoLabelsAvisos') ? Aviso::getTipoLabelsAvisos() : [],
        'avisoTipos'          => method_exists('Aviso', 'getTipos') ? Aviso::getTipos() : [],
        'f_nome'              => $nomeFiltro,
        'f_tipo'              => $tipoFiltro,
        'f_plano_nome'        => $planoNomeFiltro,
        'f_modalidade_nome'   => $modalidadeNomeFiltro,
        'f_aula_nome'         => $aulaNomeFiltro,
        'f_filial_nome'       => $filialNomeFiltro,
    ];

    render('admin/painel', 'Painel Administrativo', $data);
}
