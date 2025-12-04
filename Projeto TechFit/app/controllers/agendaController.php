<?php 
function cancelarAgendamento($id_aula, $id_aluno){
    $pdo = Connect::conectar();
    
    $sql = "UPDATE Agendamento SET 
    status='cancelado' WHERE id_aula = :id_aula AND id_aluno = :id_aluno";
    $sql = $pdo->prepare($sql);
    $sql->execute([":id_aula" => $id_aula, ":id_aluno" => $id_aluno]);

    // após cancelar, promover primeiro da lista de espera (se houver)
    // verificar vagas agora disponíveis
    $stmtIns = $pdo->prepare('SELECT COUNT(*) FROM Agendamento WHERE id_aula = :id_aula AND status = "agendado"');
    $stmtIns->execute([':id_aula' => $id_aula]);
    $inscritos = (int)$stmtIns->fetchColumn();

    $stmtCap = $pdo->prepare('SELECT quantidade_pessoas FROM Aulas WHERE id_aula = :id');
    $stmtCap->execute([':id' => $id_aula]);
    $capacidade = (int)$stmtCap->fetchColumn();

    if ($inscritos < $capacidade) {
        // promover o primeiro da fila (menor data_agendamento)
        $sel = $pdo->prepare('SELECT id_agendamento, id_aluno FROM Agendamento WHERE id_aula = :id_aula AND status = "espera" ORDER BY data_agendamento ASC LIMIT 1');
        $sel->execute([':id_aula' => $id_aula]);
        $next = $sel->fetch(PDO::FETCH_ASSOC);
        if ($next) {
            $upd = $pdo->prepare('UPDATE Agendamento SET status = "agendado" WHERE id_agendamento = :id');
            $upd->execute([':id' => $next['id_agendamento']]);
            // opcional: notificar usuário (deixar para feature futura)
        }
    }
}