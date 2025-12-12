<?php require_once __DIR__ . "/../partials/nav.php"; ?>

<div class="container my-4">
    <h1 class="mb-4">Painel Administrativo</h1>

    <?php
        // Exibe mensagens de flash (success, error, warning, info)
        if (function_exists('has_flash') && function_exists('get_flash')) {
            $flashTypes = [
                'success' => 'success',
                'error'   => 'danger',
                'warning' => 'warning',
                'info'    => 'info',
            ];

            foreach ($flashTypes as $type => $bootstrapClass) {
                if (has_flash($type)) {
                    foreach (get_flash($type) as $msg) {
                        echo '<div class="alert alert-' . $bootstrapClass . '\">' . htmlspecialchars($msg) . '</div>';
                    }
                }
            }
        }
    ?>

    <!-- Barra de tarefas / Tabs -->
    <ul class="nav nav-tabs mb-3" id="adminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button" role="tab">Usuários</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="planos-tab" data-bs-toggle="tab" data-bs-target="#planos" type="button" role="tab">Planos</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="modalidades-tab" data-bs-toggle="tab" data-bs-target="#modalidades" type="button" role="tab">Modalidades</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="aulas-tab" data-bs-toggle="tab" data-bs-target="#aulas" type="button" role="tab">Aulas</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="comunicados-tab" data-bs-toggle="tab" data-bs-target="#comunicados" type="button" role="tab">Comunicados</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="filiais-tab" data-bs-toggle="tab" data-bs-target="#filiais" type="button" role="tab">Filiais</button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Usuarios -->
        <div class="tab-pane fade show active p-3 bg-white rounded shadow-sm" id="usuarios" role="tabpanel">
            <h3>Gerenciar Usuários</h3>

            <div class="mb-4">
                <h5>Adicionar usuário</h5>
                <form method="post" class="row g-2" id="form-add-usuario">
                    <input type="hidden" name="action" value="add_user">
                    <div class="col-md-3">
                        <input id="add_nome" name="nome" class="form-control" placeholder="Nome">
                    </div>
                    <div class="col-md-3">
                        <input id="add_email" name="email" class="form-control" placeholder="Email">
                    </div>
                    <div class="col-md-3">
                        <input id="add_data_nascimento" name="data_nascimento"  type="date" class="form-control" placeholder="DD/MM/YYYY">
                    </div>
                    <div class="col-md-2">
                        <input id="add_cpf" name="cpf" class="form-control" placeholder="CPF">
                    </div>
                    <div class="col-md-2">
                        <select name="tipo" id="tipo-usuario" class="form-select">
                            <option value="aluno">Aluno</option>
                            <option value="funcionario">Funcionário</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input id="add_senha" name="senha" type="password" class="form-control" placeholder="Senha">
                    </div>

                    <!-- Campos específicos para ALUNO -->
                    <div class="col-md-3 mt-2 tipo-aluno-field">
                        <select name="genero_aluno" class="form-select">
                            <option value="">Gênero (aluno)</option>
                            <option value="Feminino">Feminino</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>
                    <div class="col-md-4 mt-2 tipo-aluno-field">
                        <input name="endereco_aluno" class="form-control" placeholder="Endereço (aluno)">
                    </div>
                    <div class="col-md-3 mt-2 tipo-aluno-field">
                        <input name="telefone_aluno" class="form-control" placeholder="Telefone (aluno)">
                    </div>

                    <!-- Campos específicos para FUNCIONÁRIO -->
                    <div class="col-md-2 mt-2 tipo-func-field d-none">
                        <input name="salario_func" class="form-control" placeholder="Salário (funcionário)">
                    </div>
                    <div class="col-md-2 mt-2 tipo-func-field d-none">
                        <input name="carga_horaria_func" class="form-control" placeholder="Carga horária">
                    </div>
                    <div class="col-md-3 mt-2 tipo-func-field d-none">
                        <input name="cargo_func" class="form-control" placeholder="Cargo">
                    </div>

                    <div class="col-12 mt-2"><button class="btn btn-primary">Adicionar</button></div>
                </form>
            </div>

            <h5>Todos os usuários</h5>

            <form method="get" class="row g-2 mb-3">
                <input type="hidden" name="active_tab" value="usuarios">
                <div class="col-md-4">
                    <input name="f_nome" class="form-control" placeholder="Filtrar por nome"
                           value="<?php echo htmlspecialchars($f_nome ?? ''); ?>">
                </div>
                <div class="col-md-3">
                    <select name="f_tipo" class="form-select">
                        <option value="">Todos os tipos</option>
                        <option value="aluno"                                              <?php echo(($f_tipo ?? '') === 'aluno') ? 'selected' : ''; ?>>Aluno</option>
                        <option value="funcionario"                                                    <?php echo(($f_tipo ?? '') === 'funcionario') ? 'selected' : ''; ?>>Funcionário</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100">Filtrar</button>
                </div>
                <div class="col-md-2">
                    <a href="/admin/painel?active_tab=usuarios" class="btn btn-outline-secondary w-100">Limpar</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr><th>ID</th><th>Nome</th><th>Email</th><th>CPF</th><th>Tipo</th><th>Ações</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>

                            <tr>
                                <td><?php echo htmlspecialchars($u['id_usuario']); ?></td>
                                <td><?php echo htmlspecialchars($u['nome']); ?></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><?php echo htmlspecialchars($u['cpf']); ?></td>
                                <td><?php echo htmlspecialchars($u['tipo']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-secondary" onclick="openEdit(
                                        <?php echo (int) $u['id_usuario']; ?>,
                                        '<?php echo htmlspecialchars(addslashes($u['nome'])); ?>',
                                        '<?php echo htmlspecialchars(addslashes($u['email'])); ?>',
                                        '<?php echo htmlspecialchars($u['tipo']); ?>',
                                        '<?php echo htmlspecialchars(addslashes($u['genero_aluno'] ?? '')); ?>',
                                        '<?php echo htmlspecialchars(addslashes($u['endereco_aluno'] ?? '')); ?>',
                                        '<?php echo htmlspecialchars(addslashes($u['telefone_aluno'] ?? '')); ?>',
                                        '<?php echo htmlspecialchars($u['salario_func'] ?? ''); ?>',
                                        '<?php echo htmlspecialchars($u['carga_horaria_func'] ?? ''); ?>',
                                        '<?php echo htmlspecialchars(addslashes($u['cargo_func'] ?? '')); ?>'
                                    )">Editar</button>
                                    <form method="post" style="display:inline" onsubmit="return confirm('Remover este usuário?');">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="id_usuario" value="<?php echo (int) $u['id_usuario']; ?>">
                                        <button class="btn btn-sm btn-danger">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Modal / Form de edição simples -->
            <div id="editModal" class="modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="post">
                            <input type="hidden" name="action" value="edit_user">
                            <input type="hidden" id="edit_id" name="id_usuario" value="">
                            <div class="modal-header"><h5 class="modal-title">Editar Usuário</h5></div>
                            <div class="modal-body">
                                <div class="mb-2"><label>Nome</label><input id="edit_nome" name="nome" class="form-control"></div>
                                <div class="mb-2"><label>Email</label><input id="edit_email" name="email" class="form-control"></div>
                                <div class="mb-2"><label>Tipo</label>
                                    <select id="edit_tipo" name="tipo" class="form-select">
                                        <option value="aluno">Aluno</option>
                                        <option value="funcionario">Funcionário</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label for="">Senha</label>
                                    <input class="form-control" type="password" name="senha_hash" id="edit_pass">
                                </div>

                                <!-- Campos específicos para ALUNO no modal de edição -->
                                <div class="mb-2 tipo-aluno-field-edit">
                                    <label>Gênero (aluno)</label>
                                    <select id="edit_genero_aluno" name="edit_genero_aluno" class="form-select">
                                        <option value="">Selecione</option>
                                        <option value="Feminino">Feminino</option>
                                        <option value="Masculino">Masculino</option>
                                        <option value="Outro">Outro</option>
                                        <option value="N/D">N/D</option>
                                    </select>
                                </div>
                                <div class="mb-2 tipo-aluno-field-edit">
                                    <label>Endereço (aluno)</label>
                                    <input id="edit_endereco_aluno" name="edit_endereco_aluno" class="form-control">
                                </div>
                                <div class="mb-2 tipo-aluno-field-edit">
                                    <label>Telefone (aluno)</label>
                                    <input id="edit_telefone_aluno" name="edit_telefone_aluno" class="form-control">
                                </div>

                                <div class="mb-2 tipo-func-field-edit d-none">
                                    <label>Salário (funcionário)</label>
                                    <input id="edit_salario_func" name="edit_salario_func" class="form-control">
                                </div>
                                <div class="mb-2 tipo-func-field-edit d-none">
                                    <label>Carga horária</label>
                                    <input id="edit_carga_horaria_func" name="edit_carga_horaria_func" class="form-control">
                                </div>
                                <div class="mb-2 tipo-func-field-edit d-none">
                                    <label>Cargo</label>
                                    <input id="edit_cargo_func" name="edit_cargo_func" class="form-control">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="closeEdit()">Fechar</button>
                                <button class="btn btn-primary">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- Planos -->
        <div class="tab-pane fade p-3 bg-white rounded shadow-sm" id="planos" role="tabpanel">
            <h3>Gerenciar Planos</h3>
            <div class="mb-4">
                <h5>Adicionar plano</h5>
                <form method="post" class="row g-2">
                    <input type="hidden" name="action" value="add_plano">
                    <div class="col-md-3"><input name="nome_plano" class="form-control" placeholder="Nome do plano"></div>
                    <div class="col-md-4"><input name="descricao_plano" class="form-control" placeholder="Descrição"></div>
                    <div class="col-md-2"><input name="preco" type="number" step="0.01" class="form-control" placeholder="Preço"></div>
                    <div class="col-md-2"><input name="duracao" type="number" class="form-control" placeholder="Duração (dias)"></div>
                    <div class="col-12 mt-2"><button class="btn btn-primary">Adicionar Plano</button></div>
                </form>
            </div>

            <h5>Planos cadastrados</h5>

            <form method="get" class="row g-2 mb-3">
                <input type="hidden" name="active_tab" value="planos">
                <div class="col-md-4">
                    <input name="f_plano_nome" class="form-control" placeholder="Filtrar por nome do plano"
                           value="<?php echo htmlspecialchars($f_plano_nome ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100">Filtrar</button>
                </div>
                <div class="col-md-2">
                    <a href="/admin/painel?active_tab=planos" class="btn btn-outline-secondary w-100">Limpar</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>ID</th><th>Nome</th><th>Descrição</th><th>Preço</th><th>Duração</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php foreach ($planos ?? [] as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['id_plano']); ?></td>
                                <td><?php echo htmlspecialchars($p['nome_plano']); ?></td>
                                <td><?php echo htmlspecialchars($p['descricao_plano']); ?></td>
                                <td><?php echo htmlspecialchars($p['preco']); ?></td>
                                <td><?php echo htmlspecialchars($p['duracao']); ?></td>
                                <td>
                                    <form method="post" style="display:inline" onsubmit="return confirm('Remover plano?');">
                                        <input type="hidden" name="action" value="delete_plano">
                                        <input type="hidden" name="id_plano" value="<?php echo (int) $p['id_plano']; ?>">
                                        <button class="btn btn-sm btn-danger">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modalidades -->
        <div class="tab-pane fade p-3 bg-white rounded shadow-sm" id="modalidades" role="tabpanel">
            <h3>Gerenciar Modalidades</h3>

            <div class="mb-4">
                <h5>Adicionar Modalidade</h5>
                <form method="post" class="row g-2">
                    <input type="hidden" name="action" value="add_modalidade">
                    <div class="col-md-4"><input name="nome_modalidade" class="form-control" placeholder="Nome da modalidade"></div>
                    <div class="col-md-6"><input name="descricao_modalidade" class="form-control" placeholder="Descrição (opcional)"></div>
                    <div class="col-md-2"><button class="btn btn-primary">Adicionar</button></div>
                </form>
            </div>

            <h5>Modalidades cadastradas</h5>

            <form method="get" class="row g-2 mb-3">
                <input type="hidden" name="active_tab" value="modalidades">
                <div class="col-md-4">
                    <input name="f_modalidade_nome" class="form-control" placeholder="Filtrar por nome da modalidade"
                           value="<?php echo htmlspecialchars($f_modalidade_nome ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100">Filtrar</button>
                </div>
                <div class="col-md-2">
                    <a href="/admin/painel?active_tab=modalidades" class="btn btn-outline-secondary w-100">Limpar</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>ID</th><th>Nome</th><th>Descrição</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php foreach ($modalidades ?? [] as $m): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($m['id_modalidade']); ?></td>
                                <td><?php echo htmlspecialchars($m['nome_modalidade']); ?></td>
                                <td><?php echo htmlspecialchars($m['descricao'] ?? ''); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-secondary" onclick="openModalidadeEdit(<?php echo (int) $m['id_modalidade']; ?>, '<?php echo htmlspecialchars(addslashes($m['nome_modalidade'])); ?>', '<?php echo htmlspecialchars(addslashes($m['descricao'] ?? '')); ?>')">Editar</button>
                                    <form method="post" style="display:inline" onsubmit="return confirm('Remover modalidade?');">
                                        <input type="hidden" name="action" value="delete_modalidade">
                                        <input type="hidden" name="id_modalidade" value="<?php echo (int) $m['id_modalidade']; ?>">
                                        <button class="btn btn-sm btn-danger">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- editar modalidade modal -->
            <div id="editModalidadeModal" class="modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="post">
                            <input type="hidden" name="action" value="edit_modalidade">
                            <input type="hidden" id="edit_modal_id" name="id_modalidade" value="">
                            <div class="modal-header"><h5 class="modal-title">Editar Modalidade</h5></div>
                            <div class="modal-body">
                                <div class="mb-2"><label>Nome</label><input id="edit_modal_nome" name="nome_modalidade" class="form-control"></div>
                                <div class="mb-2"><label>Descrição</label><input id="edit_modal_desc" name="descricao_modalidade" class="form-control"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="closeEditModalidade()">Fechar</button>
                                <button class="btn btn-primary">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- Aulas -->
        <div class="tab-pane fade p-3 bg-white rounded shadow-sm" id="aulas" role="tabpanel">
            <h3>Gerenciar Aulas</h3>
            <div class="mb-4">
                <h5>Adicionar Aula</h5>
                <form method="post" class="row g-2">
                    <input type="hidden" name="action" value="add_aula">
                    <div class="col-md-3"><input name="nome_aula" class="form-control" placeholder="Nome da aula"></div>
                    <div class="col-md-3"><input name="dia_aula" type="datetime-local" class="form-control"></div>
                    <div class="col-md-2"><input name="quantidade" type="number" class="form-control" placeholder="Vagas"></div>
                    <div class="col-md-2">
                        <select name="id_funcionario_aula" class="form-select">
                            <option value="">-- Funcionário --</option>
                            <?php foreach ($funcionarios ?? [] as $ff): ?>
                                <option value="<?php echo (int) $ff['id_funcionario']; ?>">
                                    <?php echo htmlspecialchars($ff['id_funcionario'] . ' - ' . $ff['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="id_modalidade_aula" class="form-select">
                            <option value="">-- Modalidade --</option>
                            <?php foreach ($modalidades ?? [] as $mm): ?>
                                <option value="<?php echo (int) $mm['id_modalidade']; ?>"><?php echo htmlspecialchars($mm['nome_modalidade']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="id_filial_aula" class="form-select">
                            <option value="">-- Filial --</option>
                            <?php foreach ($filiais ?? [] as $fi): ?>
                                <option value="<?php echo (int) $fi['id_filial']; ?>"><?php echo htmlspecialchars($fi['nome_filial']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <textarea class="border-1 w-100 rounded-lg" name="descricao_aula" placeholder="Descrição da aula" id=""></textarea>
                    </div>
                    <div class="col-12 mt-2"><button class="btn btn-primary">Adicionar Aula</button></div>
                </form>
            </div>

            <h5>Aulas existentes</h5>

            <form method="get" class="row g-2 mb-3">
                <input type="hidden" name="active_tab" value="aulas">
                <div class="col-md-4">
                    <input name="f_aula_nome" class="form-control" placeholder="Filtrar por nome da aula"
                           value="<?php echo htmlspecialchars($f_aula_nome ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100">Filtrar</button>
                </div>
                <div class="col-md-2">
                    <a href="/admin/painel?active_tab=aulas" class="btn btn-outline-secondary w-100">Limpar</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>ID</th><th>Nome</th><th>Data</th><th>Modalidade</th><th>Filial</th><th>Descrição</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php foreach ($aulas ?? [] as $a): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($a['id_aula']); ?></td>
                                <td><?php echo htmlspecialchars($a['nome_aula']); ?></td>
                                <td><?php echo htmlspecialchars($a['dia_aula']); ?></td>
                                <td><?php echo htmlspecialchars($a['nome_modalidade'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($a['nome_filial'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($a['descricao'])?></td>
                                <td>
                                    <form method="post" style="display:inline" onsubmit="return confirm('Remover aula?');">
                                        <input type="hidden" name="action" value="delete_aula">
                                        <input type="hidden" name="id_aula" value="<?php echo (int) $a['id_aula']; ?>">
                                        <button class="btn btn-sm btn-danger">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Filiais -->
        <div class="tab-pane fade p-3 bg-white rounded shadow-sm" id="filiais" role="tabpanel">
            <h3>Gerenciar Filiais</h3>

            <div class="mb-4">
                <h5>Adicionar Filial</h5>
                <form method="post" class="row g-2">
                    <input type="hidden" name="action" value="add_filial">
                    <div class="col-md-3"><input name="nome_filial" class="form-control" placeholder="Nome da filial"></div>
                    <div class="col-md-5"><input name="endereco_filial" class="form-control" placeholder="Endereço"></div>
                    <div class="col-md-2"><input name="telefone_filial" class="form-control" placeholder="Telefone"></div>
                    <div class="col-md-2"><button class="btn btn-primary">Adicionar</button></div>
                </form>
            </div>

            <h5>Filiais cadastradas</h5>

            <form method="get" class="row g-2 mb-3">
                <input type="hidden" name="active_tab" value="filiais">
                <div class="col-md-4">
                    <input name="f_filial_nome" class="form-control" placeholder="Filtrar por nome da filial"
                           value="<?php echo htmlspecialchars($f_filial_nome ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100">Filtrar</button>
                </div>
                <div class="col-md-2">
                    <a href="/admin/painel?active_tab=filiais" class="btn btn-outline-secondary w-100">Limpar</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>ID</th><th>Nome</th><th>Endereço</th><th>Telefone</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php foreach ($filiais ?? [] as $fi): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($fi['id_filial']); ?></td>
                                <td><?php echo htmlspecialchars($fi['nome_filial']); ?></td>
                                <td><?php echo htmlspecialchars($fi['endereco']); ?></td>
                                <td><?php echo htmlspecialchars($fi['telefone']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-secondary" onclick="openFilialEdit(<?php echo (int) $fi['id_filial']; ?>, '<?php echo htmlspecialchars(addslashes($fi['nome_filial'])); ?>', '<?php echo htmlspecialchars(addslashes($fi['endereco'] ?? '')); ?>', '<?php echo htmlspecialchars(addslashes($fi['telefone'] ?? '')); ?>')">Editar</button>
                                    <form method="post" style="display:inline" onsubmit="return confirm('Remover filial?');">
                                        <input type="hidden" name="action" value="delete_filial">
                                        <input type="hidden" name="id_filial" value="<?php echo (int) $fi['id_filial']; ?>">
                                        <button class="btn btn-sm btn-danger">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- editar filial modal -->
            <div id="editFilialModal" class="modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="post">
                            <input type="hidden" name="action" value="edit_filial">
                            <input type="hidden" id="edit_filial_id" name="id_filial" value="">
                            <div class="modal-header"><h5 class="modal-title">Editar Filial</h5></div>
                            <div class="modal-body">
                                <div class="mb-2"><label>Nome</label><input id="edit_filial_nome" name="nome_filial" class="form-control"></div>
                                <div class="mb-2"><label>Endereço</label><input id="edit_filial_end" name="endereco_filial" class="form-control"></div>
                                <div class="mb-2"><label>Telefone</label><input id="edit_filial_tel" name="telefone_filial" class="form-control"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="closeEditFilial()">Fechar</button>
                                <button class="btn btn-primary">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>

        <!-- Comunicados (reaproveita a view existente) -->
        <div class="tab-pane fade p-3 bg-white rounded shadow-sm" id="comunicados" role="tabpanel">
            <h3>Gerenciar Comunicados</h3>
            <?php require __DIR__ . '/comunicadoView.php'; ?>
        </div>

    </div>
</div>

<script>
function openEdit(id, nome, email, tipo, generoAluno, enderecoAluno, telefoneAluno, salarioFunc, cargaFunc, cargoFunc) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nome').value = nome;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_tipo').value = tipo;

    var generoSelect = document.getElementById('edit_genero_aluno');
    var endInput     = document.getElementById('edit_endereco_aluno');
    var telInput     = document.getElementById('edit_telefone_aluno');
    var salInput     = document.getElementById('edit_salario_func');
    var cargaInput   = document.getElementById('edit_carga_horaria_func');
    var cargoInput   = document.getElementById('edit_cargo_func');

    if (generoSelect) generoSelect.value = generoAluno || '';
    if (endInput)     endInput.value     = enderecoAluno || '';
    if (telInput)     telInput.value     = telefoneAluno || '';
    if (salInput)     salInput.value     = salarioFunc   || '';
    if (cargaInput)   cargaInput.value   = cargaFunc     || '';
    if (cargoInput)   cargoInput.value   = cargoFunc     || '';

    atualizarCamposEditPorTipo();

    var modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}
function closeEdit(){
    var modalEl = document.getElementById('editModal');
    var modal = bootstrap.Modal.getInstance(modalEl);
    if(modal) modal.hide();
}

function openModalidadeEdit(id, nome, descricao){
    document.getElementById('edit_modal_id').value = id;
    document.getElementById('edit_modal_nome').value = nome;
    document.getElementById('edit_modal_desc').value = descricao;
    var modal = new bootstrap.Modal(document.getElementById('editModalidadeModal'));
    modal.show();
}
function closeEditModalidade(){
    var modalEl = document.getElementById('editModalidadeModal');
    var modal = bootstrap.Modal.getInstance(modalEl);
    if(modal) modal.hide();
}

function openFilialEdit(id, nome, endereco, telefone){
    document.getElementById('edit_filial_id').value = id;
    document.getElementById('edit_filial_nome').value = nome;
    document.getElementById('edit_filial_end').value = endereco;
    document.getElementById('edit_filial_tel').value = telefone;
    var modal = new bootstrap.Modal(document.getElementById('editFilialModal'));
    modal.show();
}
function closeEditFilial(){
    var modalEl = document.getElementById('editFilialModal');
    var modal = bootstrap.Modal.getInstance(modalEl);
    if(modal) modal.hide();
}

// Toggle campos no modal de edição conforme tipo
const editTipoSelect   = document.getElementById('edit_tipo');
const alunoFieldsEdit  = document.querySelectorAll('.tipo-aluno-field-edit');
const funcFieldsEdit   = document.querySelectorAll('.tipo-func-field-edit');

function atualizarCamposEditPorTipo() {
    const tipo = editTipoSelect ? editTipoSelect.value : 'aluno';

    if (tipo === 'funcionario') {
        alunoFieldsEdit.forEach(el => el.classList.add('d-none'));
        funcFieldsEdit.forEach(el => el.classList.remove('d-none'));
    } else {
        funcFieldsEdit.forEach(el => el.classList.add('d-none'));
        alunoFieldsEdit.forEach(el => el.classList.remove('d-none'));
    }
}

if (editTipoSelect) {
    editTipoSelect.addEventListener('change', atualizarCamposEditPorTipo);
}


document.addEventListener('DOMContentLoaded', function(){
    const params = new URLSearchParams(window.location.search);
    const active = params.get('active_tab');
    if (active) {
        const btn = document.querySelector(`#${active}-tab`);
        if (btn) {
            new bootstrap.Tab(btn).show();
        }
    }

    // Atualiza o parâmetro ?active_tab na URL ao trocar de aba
    document.querySelectorAll('#adminTabs button[data-bs-toggle="tab"]').forEach(function (btn) {
        btn.addEventListener('shown.bs.tab', function () {
            const tabId = this.id.replace('-tab', '');
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('active_tab', tabId);
            const newUrl = window.location.pathname + '?' + urlParams.toString();
            window.history.replaceState({}, '', newUrl);
        });
    });

    const telefoneInputAdm = document.querySelector('input[name="telefone_aluno"]');
    if(telefoneInputAdm){
            telefoneInputAdm.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 11) value = value.substring(0, 11);
                if (value.length > 7) {
                    value = '(' + value.substring(0, 2) + ') ' + value.substring(2, 7) + '-' + value.substring(7);
                } else if (value.length > 2) {
                    value = '(' + value.substring(0, 2) + ') ' + value.substring(2);
                }
            this.value = value;
            });
    }
    const telefoneEditAluno = document.querySelector('input[name="edit_telefone_aluno"]');
    if(telefoneEditAluno){
        telefoneEditAluno.addEventListener('input', function(){
            let val = this.value.replace(/\D/g, '');
            if(val.length > 11) val = val.substring(0, 11);
            if(val.length > 7){
                val = '(' + val.substring(0, 2) + ') ' + val.substring(2, 7) + '-' + val.substring(7);
            }else if (val.length > 2){
                val = '(' + val.substring(0, 2) + ') ' + val.substring(2);
            }
            this.value = val;
        })
    }
    // Máscara de CPF para o formulário de adicionar usuário
    const cpfInputAdmin = document.querySelector('input[name="cpf"]');
    if (cpfInputAdmin) {
        cpfInputAdmin.addEventListener('input', function () {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 11) value = value.substring(0, 11);

            if (value.length > 8) {
                value = value.substring(0, 3) + '.' + value.substring(3, 6) + '.' + value.substring(6, 9) + '-' + value.substring(9);
            } else if (value.length > 6) {
                value = value.substring(0, 3) + '.' + value.substring(3, 6) + '.' + value.substring(6);
            } else if (value.length > 3) {
                value = value.substring(0, 3) + '.' + value.substring(3);
            }

            this.value = value;
        });
    }

    // Exibe campos diferentes conforme o tipo selecionado (aluno / funcionario)
    const tipoSelect = document.getElementById('tipo-usuario');
    const alunoFields = document.querySelectorAll('.tipo-aluno-field');
    const funcFields  = document.querySelectorAll('.tipo-func-field');

    function atualizarCamposPorTipo() {
        const tipo = tipoSelect ? tipoSelect.value : 'aluno';

        if (tipo === 'funcionario') {
            alunoFields.forEach(el => el.classList.add('d-none'));
            funcFields.forEach(el => el.classList.remove('d-none'));
        } else {
            funcFields.forEach(el => el.classList.add('d-none'));
            alunoFields.forEach(el => el.classList.remove('d-none'));
        }
    }

    if (tipoSelect) {
        tipoSelect.addEventListener('change', atualizarCamposPorTipo);
        atualizarCamposPorTipo();
    }

    // Validação em tempo real do formulário de adicionar usuário
    const formAddUsuario = document.getElementById('form-add-usuario');
    if (formAddUsuario) {
        const nomeInput   = document.getElementById('add_nome');
        const emailInput  = document.getElementById('add_email');
        const dataInput   = document.getElementById('add_data_nascimento');
        const cpfInput    = document.getElementById('add_cpf');
        const senhaInput  = document.getElementById('add_senha');

        function showError(input, message) {
            if (!input) return;
            input.classList.add('is-invalid');
            let feedback = input.parentNode.querySelector('.invalid-feedback');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback d-block';
                input.parentNode.appendChild(feedback);
            }
            feedback.textContent = message;
        }

        function clearError(input) {
            if (!input) return;
            input.classList.remove('is-invalid');
            const feedback = input.parentNode.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.textContent = '';
            }
        }

        function validarNome() {
            const v = (nomeInput?.value || '').trim();
            if (v.length === 0) {
                showError(nomeInput, 'Nome é obrigatório.');
                return false;
            }
            if (v.length < 3) {
                showError(nomeInput, 'Nome deve ter pelo menos 3 caracteres.');
                return false;
            }
            clearError(nomeInput);
            return true;
        }

        function validarEmail() {
            const v = (emailInput?.value || '').trim();
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (v.length === 0) {
                showError(emailInput, 'Email é obrigatório.');
                return false;
            }
            if (!regex.test(v)) {
                showError(emailInput, 'Email inválido.');
                return false;
            }
            clearError(emailInput);
            return true;
        }

        function validarData() {
            const v = (dataInput?.value || '').trim();
            if (v.length === 0) {
                showError(dataInput, 'Data de nascimento é obrigatória.');
                return false;
            }
            clearError(dataInput);
            return true;
        }

        function validarCPF() {
            let v = (cpfInput?.value || '').replace(/\D/g, '');
            if (v.length === 0) {
                showError(cpfInput, 'CPF é obrigatório.');
                return false;
            }
            if (v.length !== 11) {
                showError(cpfInput, 'CPF deve conter 11 dígitos.');
                return false;
            }
            clearError(cpfInput);
            return true;
        }

        function validarSenha() {
            const v = (senhaInput?.value || '').trim();
            if (v.length === 0) {
                showError(senhaInput, 'Senha é obrigatória.');
                return false;
            }
            if (v.length < 8) {
                showError(senhaInput, 'Senha deve ter pelo menos 8 caracteres.');
                return false;
            }
            clearError(senhaInput);
            return true;
        }

        [nomeInput, emailInput, dataInput, cpfInput, senhaInput].forEach(function (input) {
            if (!input) return;
            input.addEventListener('input', function () {
                switch (input) {
                    case nomeInput:   validarNome(); break;
                    case emailInput:  validarEmail(); break;
                    case dataInput:   validarData(); break;
                    case cpfInput:    validarCPF(); break;
                    case senhaInput:  validarSenha(); break;
                }
            });
            input.addEventListener('blur', function () {
                switch (input) {
                    case nomeInput:   validarNome(); break;
                    case emailInput:  validarEmail(); break;
                    case dataInput:   validarData(); break;
                    case cpfInput:    validarCPF(); break;
                    case senhaInput:  validarSenha(); break;
                }
            });
        });

        formAddUsuario.addEventListener('submit', function (e) {
            const okNome   = validarNome();
            const okEmail  = validarEmail();
            const okData   = validarData();
            const okCPF    = validarCPF();
            const okSenha  = validarSenha();
            if (!okNome || !okEmail || !okData || !okCPF || !okSenha) {
                e.preventDefault();
            }
        });
    }

});
</script>
