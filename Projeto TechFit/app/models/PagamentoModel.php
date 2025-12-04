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

        // se não existe aluno, não tentar inserir colunas inexistentes (schema atual não tem nome_aluno/email em Alunos)
        // forçar o usuário a completar o cadastro via perfil
        if ($idAluno === null) {
            return ['sucesso' => false, 'erro' => 'Aluno não encontrado. Complete seu cadastro no perfil antes de pagar.'];
        }

        // se não foi possível obter/crear um id_aluno válido, abortar e retornar erro
        if ($idAluno === null) {
            return ['sucesso' => false, 'erro' => 'Aluno não encontrado e não foi possível criar registro de aluno. Complete seu cadastro no perfil antes de pagar.'];
        }

        // Inserir registro em Pagamentos
        $sqlIns = "INSERT INTO Pagamentos (status, data_pagamento, valor, metodo_pagamento, id_aluno) VALUES (:status, :data_pagamento, :valor, :metodo, :id_aluno)";
        try {
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
        } catch (Exception $e) {
            return ['sucesso' => false, 'erro' => 'Erro ao registrar pagamento: ' . $e->getMessage()];
        }

        return ['sucesso' => true, 'pagamento_id' => (int)$pdo->lastInsertId(), 'id_aluno' => $idAluno];
    }
}

?>
