<?php 
function cancelarAgendamento($id_aula, $id_aluno){
    $pdo = Connect::conectar();
    
    $sql = "UPDATE agendamento SET status = 'cancelado' WHERE id_aula = :id_aula AND id_aluno = :id_aluno";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_aula' => $id_aula, ':id_aluno' => $id_aluno]);
}
