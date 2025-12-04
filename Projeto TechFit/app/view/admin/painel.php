<?php require_once __DIR__ . "/../partials/nav.php"; ?>

<div class="container my-4">
    <h1 class="mb-4">Painel Administrativo</h1>

    <!-- Barra de tarefas / Tabs -->
    <ul class="nav nav-tabs mb-3" id="adminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button" role="tab">Usuários</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="funcionarios-tab" data-bs-toggle="tab" data-bs-target="#funcionarios" type="button" role="tab">Funcionários</button>
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

            <?php if (!empty($messages['usuarios']['errors'] ?? [])): ?>
                <div class="alert alert-danger"><?php echo implode("<br>", $messages['usuarios']['errors']); ?></div>
            <?php endif; ?>
            <?php if (!empty($messages['usuarios']['success'] ?? null)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($messages['usuarios']['success']); ?></div>
            <?php endif; ?>

            <div class="mb-4">
                <h5>Adicionar usuário</h5>
                <form method="post" class="row g-2">
                    <input type="hidden" name="action" value="add_user">
                    <div class="col-md-3"><input name="nome" class="form-control" placeholder="Nome"></div>
                    <div class="col-md-3"><input name="email" class="form-control" placeholder="Email"></div>
                    <div class="col-md-2"><input name="cpf" class="form-control" placeholder="CPF"></div>
                    <div class="col-md-2">
                        <select name="tipo" class="form-select">
                            <option value="aluno">Aluno</option>
                            <option value="funcionario">Funcionário</option>
                        </select>
                    </div>
                    <div class="col-md-2"><input name="senha" type="password" class="form-control" placeholder="Senha"></div>
                    <div class="col-12 mt-2"><button class="btn btn-primary">Adicionar</button></div>
                </form>
            </div>

            <h5>Todos os usuários</h5>
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
                                    <button class="btn btn-sm btn-secondary" onclick="openEdit(<?php echo (int)$u['id_usuario']; ?>,'<?php echo htmlspecialchars(addslashes($u['nome'])); ?>','<?php echo htmlspecialchars(addslashes($u['email'])); ?>','<?php echo htmlspecialchars($u['tipo']); ?>')">Editar</button>
                                    <form method="post" style="display:inline" onsubmit="return confirm('Remover este usuário?');">
                                        <input type="hidden" name="action" value="delete_user">
                                        <input type="hidden" name="id_usuario" value="<?php echo (int)$u['id_usuario']; ?>">
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
                                    <select id="edit_tipo" name="tipo" class="form-select"><option value="aluno">Aluno</option><option value="funcionario">Funcionário</option></select>
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

        <!-- Funcionarios (mesma interface por enquanto) -->
            
        <div class="tab-pane fade p-3 bg-white rounded shadow-sm" id="funcionarios" role="tabpanel">
            <h3>Gerenciar Funcionários</h3>
            <?php if (!empty($messages['funcionarios']['errors'] ?? [])): ?>
                <div class="alert alert-danger"><?php echo implode("<br>", $messages['funcionarios']['errors']); ?></div>
            <?php endif; ?>
            <?php if (!empty($messages['funcionarios']['success'] ?? null)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($messages['funcionarios']['success']); ?></div>
            <?php endif; ?>

            <div class="mb-4">
                <h5>Adicionar funcionário</h5>
                <form method="post" class="row g-2">
                    <input type="hidden" name="action" value="add_funcionario">
                    <div class="col-md-3"><input name="nome_func" class="form-control" placeholder="Nome (informativo)"></div>
                    <div class="col-md-3"><input name="cpf_func" class="form-control" placeholder="CPF"></div>
                    <div class="col-md-2"><input name="salario" class="form-control" placeholder="Salário"></div>
                    <div class="col-md-2"><input name="cargo" class="form-control" placeholder="Cargo"></div>
                    <div class="col-md-2"><input name="carga_horaria" class="form-control" placeholder="Carga horária (ex: 40)"></div>
                    <div class="col-md-2"><input name="id_usuario_func" class="form-control" placeholder="ID usuário (vínculo)"></div>
                    <div class="col-12 mt-2"><button class="btn btn-primary">Adicionar</button></div>
                </form>
            </div>

            <!-- editar funcionario modal -->
            <div id="editFuncModal" class="modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="post">
                            <input type="hidden" name="action" value="edit_funcionario">
                            <input type="hidden" id="edit_func_id" name="id_funcionario" value="">
                            <div class="modal-header"><h5 class="modal-title">Editar Funcionário</h5></div>
                            <div class="modal-body">
                                <div class="mb-2"><label>Salário</label><input id="edit_salario" name="salario" class="form-control"></div>
                                <div class="mb-2"><label>Carga Horária</label><input id="edit_carga" name="carga_horaria" class="form-control"></div>
                                <div class="mb-2"><label>Cargo</label><input id="edit_cargo" name="cargo" class="form-control"></div>
                                <div class="mb-2"><label>ID Usuário</label><input id="edit_id_usuario_func" name="id_usuario_func" class="form-control"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" onclick="closeEditFunc()">Fechar</button>
                                <button class="btn btn-primary">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <h5>Funcionários cadastrados</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead><tr><th>ID</th><th>Salário</th><th>Carga</th><th>Cargo</th><th>ID Usuario</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php foreach ($funcionarios ?? [] as $f): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($f['id_funcionario']); ?></td>
                                <td><?php echo htmlspecialchars($f['salario']); ?></td>
                                <td><?php echo htmlspecialchars($f['carga_horaria']); ?></td>
                                <td><?php echo htmlspecialchars($f['cargo']); ?></td>
                                <td><?php echo htmlspecialchars($f['id_usuario']); ?></td>
                                <td>
                                    <form method="post" style="display:inline" onsubmit="return confirm('Remover funcionário?');">
                                        <input type="hidden" name="action" value="delete_funcionario">
                                        <input type="hidden" name="id_funcionario" value="<?php echo (int)$f['id_funcionario']; ?>">
                                        <button class="btn btn-sm btn-danger">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
                                        <input type="hidden" name="id_plano" value="<?php echo (int)$p['id_plano']; ?>">
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
            <?php if (!empty($messages['modalidades']['errors'] ?? [])): ?>
                <div class="alert alert-danger"><?php echo implode("<br>", $messages['modalidades']['errors']); ?></div>
            <?php endif; ?>
            <?php if (!empty($messages['modalidades']['success'] ?? null)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($messages['modalidades']['success']); ?></div>
            <?php endif; ?>

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
                                    <button class="btn btn-sm btn-secondary" onclick="openModalidadeEdit(<?php echo (int)$m['id_modalidade']; ?>, '<?php echo htmlspecialchars(addslashes($m['nome_modalidade'])); ?>', '<?php echo htmlspecialchars(addslashes($m['descricao'] ?? '')); ?>')">Editar</button>
                                    <form method="post" style="display:inline" onsubmit="return confirm('Remover modalidade?');">
                                        <input type="hidden" name="action" value="delete_modalidade">
                                        <input type="hidden" name="id_modalidade" value="<?php echo (int)$m['id_modalidade']; ?>">
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
                            <option value="">-- ID Funcionário --</option>
                            <?php foreach ($funcionarios ?? [] as $ff): ?>
                                <option value="<?php echo (int)$ff['id_funcionario']; ?>"><?php echo htmlspecialchars($ff['id_funcionario'].' - '.$ff['cargo']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="id_modalidade_aula" class="form-select">
                            <option value="">-- Modalidade --</option>
                            <?php foreach ($modalidades ?? [] as $mm): ?>
                                <option value="<?php echo (int)$mm['id_modalidade']; ?>"><?php echo htmlspecialchars($mm['nome_modalidade']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="id_filial_aula" class="form-select">
                            <option value="">-- Filial --</option>
                            <?php foreach ($filiais ?? [] as $fi): ?>
                                <option value="<?php echo (int)$fi['id_filial']; ?>"><?php echo htmlspecialchars($fi['nome_filial']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 mt-2"><button class="btn btn-primary">Adicionar Aula</button></div>
                </form>
            </div>

            <h5>Aulas existentes</h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>ID</th><th>Nome</th><th>Data</th><th>Modalidade</th><th>Filial</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php foreach ($aulas ?? [] as $a): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($a['id_aula']); ?></td>
                                <td><?php echo htmlspecialchars($a['nome_aula']); ?></td>
                                <td><?php echo htmlspecialchars($a['dia_aula']); ?></td>
                                <td><?php echo htmlspecialchars($a['nome_modalidade'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($a['nome_filial'] ?? '-'); ?></td>
                                <td>
                                    <form method="post" style="display:inline" onsubmit="return confirm('Remover aula?');">
                                        <input type="hidden" name="action" value="delete_aula">
                                        <input type="hidden" name="id_aula" value="<?php echo (int)$a['id_aula']; ?>">
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
            <?php if (!empty($messages['filiais']['errors'] ?? [])): ?>
                <div class="alert alert-danger"><?php echo implode("<br>", $messages['filiais']['errors']); ?></div>
            <?php endif; ?>
            <?php if (!empty($messages['filiais']['success'] ?? null)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($messages['filiais']['success']); ?></div>
            <?php endif; ?>

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
                                    <button class="btn btn-sm btn-secondary" onclick="openFilialEdit(<?php echo (int)$fi['id_filial']; ?>, '<?php echo htmlspecialchars(addslashes($fi['nome_filial'])); ?>', '<?php echo htmlspecialchars(addslashes($fi['endereco'] ?? '')); ?>', '<?php echo htmlspecialchars(addslashes($fi['telefone'] ?? '')); ?>')">Editar</button>
                                    <form method="post" style="display:inline" onsubmit="return confirm('Remover filial?');">
                                        <input type="hidden" name="action" value="delete_filial">
                                        <input type="hidden" name="id_filial" value="<?php echo (int)$fi['id_filial']; ?>">
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
function openEdit(id, nome, email, tipo) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nome').value = nome;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_tipo').value = tipo;
    var modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}
function closeEdit(){
    var modalEl = document.getElementById('editModal');
    var modal = bootstrap.Modal.getInstance(modalEl);
    if(modal) modal.hide();
}

function openFuncEdit(id, salario, carga, cargo, id_usuario){
    document.getElementById('edit_func_id').value = id;
    document.getElementById('edit_salario').value = salario;
    document.getElementById('edit_carga').value = carga;
    document.getElementById('edit_cargo').value = cargo;
    document.getElementById('edit_id_usuario_func').value = id_usuario;
    var modal = new bootstrap.Modal(document.getElementById('editFuncModal'));
    modal.show();
}
function closeEditFunc(){
    var modalEl = document.getElementById('editFuncModal');
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

// When returning from a POST, server will include ?active_tab=funcionarios (or other)
document.addEventListener('DOMContentLoaded', function(){
    const params = new URLSearchParams(window.location.search);
    const active = params.get('active_tab');
    if(active){
        // activate the corresponding tab button
        const btn = document.querySelector(`#${active}-tab`);
        if(btn){
            new bootstrap.Tab(btn).show();
        }
    }
});
</script>
