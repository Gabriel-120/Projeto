<?php require_once __DIR__ . "/../partials/nav.php"; ?>

<div class="container my-4">
    <h1 class="mb-4">📚 Dashboard do Instrutor</h1>
    
    <?php if (!empty($alerta_cadastro)): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>⚠️ Atenção!</strong> <?php echo htmlspecialchars($alerta_cadastro); ?>
            <a href="/profile?page=configuracao" class="alert-link">Completar cadastro</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Seção de Criação de Aulas -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">➕ Criar Nova Aula</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="?action=criar_aula">
                        <div class="mb-3">
                            <label class="form-label">Nome da Aula</label>
                            <input type="text" name="nome_aula" class="form-control" placeholder="Ex: Pilates Avançado" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data e Hora</label>
                            <input type="datetime-local" name="dia_aula" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantidade de Pessoas</label>
                            <input type="number" name="quantidade_pessoas" class="form-control" min="1" placeholder="20" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Modalidade</label>
                            <select name="id_modalidade" class="form-select" required>
                                <option value="">Selecione uma modalidade</option>
                                <?php foreach ($modalidades as $mod): ?>
                                    <option value="<?php echo (int)$mod['id_modalidade']; ?>">
                                        <?php echo htmlspecialchars($mod['nome_modalidade']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Filial</label>
                            <select name="id_filial" class="form-select" required>
                                <option value="">Selecione uma filial</option>
                                <?php foreach ($filiais as $filial): ?>
                                    <option value="<?php echo (int)$filial['id_filial']; ?>">
                                        <?php echo htmlspecialchars($filial['nome_filial']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea name="descricao" class="form-control" rows="3" placeholder="Detalhes sobre a aula..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Criar Aula</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="col-md-6">
            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <h5 class="card-title">👤 Meus Dados</h5>
                    <p class="mb-2"><strong>Nome:</strong> <?php echo htmlspecialchars($instrutor['nome'] ?? ''); ?></p>
                    <p class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($instrutor['email'] ?? ''); ?></p>
                    <p class="mb-2"><strong>Cargo:</strong> <?php echo htmlspecialchars($instrutor['cargo'] ?? ''); ?></p>
                    <p class="mb-2"><strong>Carga Horária:</strong> <?php echo htmlspecialchars($instrutor['carga_horaria'] ?? ''); ?> horas/semana</p>
                    <p class="mb-2"><strong>Salário:</strong> R$ <?php echo number_format($instrutor['salario'] ?? 0, 2, ',', '.'); ?></p>
                    <a href="/profile?page=configuracao" class="btn btn-sm btn-secondary">Editar Configurações</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Aulas Cadastradas -->
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white">
            <h5 class="card-title mb-0">📅 Minhas Aulas</h5>
        </div>
        <div class="card-body">
            <?php if (empty($aulas)): ?>
                <p class="text-muted">Nenhuma aula criada ainda. Comece criando uma!</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Aula</th>
                                <th>Data/Hora</th>
                                <th>Modalidade</th>
                                <th>Filial</th>
                                <th>Capacidade</th>
                                <th>Inscritos</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($aulas as $aula): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($aula['nome_aula']); ?></strong></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($aula['dia_aula'])); ?></td>
                                    <td><?php echo htmlspecialchars($aula['nome_modalidade'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($aula['nome_filial'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($aula['quantidade_pessoas']); ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($aula['total_alunos'] ?? 0); ?></span>
                                    </td>
                                    <td>
                                        <a href="?action=editar_aula&id=<?php echo (int)$aula['id_aula']; ?>" class="btn btn-sm btn-warning">Editar</a>
                                        <a href="?action=ver_inscritos&id=<?php echo (int)$aula['id_aula']; ?>" class="btn btn-sm btn-info">Ver Alunos</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../partials/footer.php"; ?>
