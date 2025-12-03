<?php 
function cancelarAgendamento($id_aula, $id_aluno){
    $pdo = Connect::conectar();
    
    $sql = "UPDATE Agendamento SET 
    status='cancelado' WHERE id_aula = :id_aula AND id_aluno = :id_aluno";
    $sql = $pdo->prepare($sql);
    $sql->execute([":id_aula" => $id_aula, ":id_aluno" => $id_aluno]);
}