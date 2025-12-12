<?php

function adminRelatoriosController(): void
{
    $pdo = Connect::conectar();

    $totalAlunos = (int)$pdo->query("SELECT COUNT(*) FROM alunos")->fetchColumn();

    // Alunos com plano ativo
    $alunosAtivos = (int)$pdo->query("
        SELECT COUNT(DISTINCT id_aluno)
        FROM planos_aluno
        WHERE status = 'ativo'
    ")->fetchColumn();

    // Receita total aprovada
    $receitaTotal = (float)$pdo->query("
        SELECT COALESCE(SUM(valor), 0)
        FROM pagamentos
        WHERE status = 'Aprovado'
    ")->fetchColumn();

    // Frequência por filial (usando checkin)
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

    $data = [
        'titulo'              => 'Relatórios',
        'totalAlunos'         => $totalAlunos,
        'alunosAtivos'        => $alunosAtivos,
        'receitaTotal'        => $receitaTotal,
        'frequenciaPorFilial' => $frequenciaPorFilial,
    ];

    render('admin/relatoriosView', $data['titulo'], $data);
}