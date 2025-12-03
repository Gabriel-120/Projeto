<?php
/**
 * PagamentoModel.php - operações de pagamento integradas ao esquema existente (Pagamentos)
 */
require_once __DIR__ . '/Connect.php';

class PagamentoModel {
    private static function getPDO() {
        return Connect::conectar();
    }

    public static function criarPagamentoPorUsuario(int $usuarioId, string $plano, float $preco, string $metodo = 'plano') : array {
        $pdo = self::getPDO();

        // Obter id_aluno relacionado ao usuário
        $sql = "SELECT id_aluno FROM Alunos WHERE id_usuario = :id_usuario LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_usuario' => $usuarioId]);
        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

        $idAluno = $aluno ? (int)$aluno['id_aluno'] : null;

        // Inserir registro em Pagamentos
        $sqlIns = "INSERT INTO Pagamentos (status_pagamento, data_pagamento, valor, metodo_pagamento, id_aluno) VALUES (:status, :data_pagamento, :valor, :metodo, :id_aluno)";
        $stmtIns = $pdo->prepare($sqlIns);
        $now = date('Y-m-d H:i:s');
        $status = 'confirmado';
        $stmtIns->execute([
            ':status' => $status,
            ':data_pagamento' => $now,
            ':valor' => $preco,
            ':metodo' => $plano,
            ':id_aluno' => $idAluno
        ]);

        return ['sucesso' => true, 'pagamento_id' => (int)$pdo->lastInsertId(), 'id_aluno' => $idAluno];
    }
}

?>
