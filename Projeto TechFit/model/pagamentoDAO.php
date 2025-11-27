<?php
/**
 * PagamentoDAO.php - Acesso aos dados de pagamentos no MySQL
 */

require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/pagamento.php';

class PagamentoDAO {
    private $conexao;

    public function __construct() {
        $this->conexao = Connection::getInstance()->getConexao();
    }

    /**
     * Criar novo pagamento
     */
    public function criar(Pagamento $pagamento) {
        try {
            $sql = "INSERT INTO pagamentos (usuario_id, plano, preco, status) 
                    VALUES (:usuario_id, :plano, :preco, :status)";
            
            $id = Connection::getInstance()->inserir($sql, [
                ':usuario_id' => $pagamento->getUsuarioId(),
                ':plano' => $pagamento->getPlano(),
                ':preco' => $pagamento->getPreco(),
                ':status' => $pagamento->getStatus()
            ]);

            return $id;
        } catch (Exception $e) {
            throw new Exception("Erro ao criar pagamento: " . $e->getMessage());
        }
    }

    /**
     * Buscar pagamento por ID
     */
    public function lerPorId($id) {
        try {
            $sql = "SELECT * FROM pagamentos WHERE id = :id";
            $resultado = Connection::getInstance()->buscarUm($sql, [':id' => $id]);
            
            if ($resultado) {
                $pagamento = new Pagamento($resultado['usuario_id'], $resultado['plano'], $resultado['preco']);
                $pagamento->setId($resultado['id']);
                $pagamento->setDataPagamento($resultado['data_pagamento']);
                $pagamento->setStatus($resultado['status']);
                return $pagamento;
            }
            return null;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar pagamento: " . $e->getMessage());
        }
    }

    /**
     * Buscar pagamentos por usuário ID
     */
    public function buscarPorUsuario($usuario_id) {
        try {
            $sql = "SELECT * FROM pagamentos WHERE usuario_id = :usuario_id ORDER BY data_pagamento DESC";
            return Connection::getInstance()->buscarTodos($sql, [':usuario_id' => $usuario_id]);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar pagamentos: " . $e->getMessage());
        }
    }

    /**
     * Buscar último pagamento de um usuário
     */
    public function buscarUltimoPorUsuario($usuario_id) {
        try {
            $sql = "SELECT * FROM pagamentos WHERE usuario_id = :usuario_id 
                    ORDER BY data_pagamento DESC LIMIT 1";
            return Connection::getInstance()->buscarUm($sql, [':usuario_id' => $usuario_id]);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar último pagamento: " . $e->getMessage());
        }
    }

    /**
     * Buscar todos os pagamentos
     */
    public function lerTodos() {
        try {
            $sql = "SELECT * FROM pagamentos ORDER BY data_pagamento DESC";
            return Connection::getInstance()->buscarTodos($sql);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar pagamentos: " . $e->getMessage());
        }
    }

    /**
     * Atualizar pagamento
     */
    public function atualizar(Pagamento $pagamento) {
        try {
            $sql = "UPDATE pagamentos SET plano = :plano, preco = :preco, status = :status 
                    WHERE id = :id";
            
            Connection::getInstance()->executar($sql, [
                ':plano' => $pagamento->getPlano(),
                ':preco' => $pagamento->getPreco(),
                ':status' => $pagamento->getStatus(),
                ':id' => $pagamento->getId()
            ]);

            return true;
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar pagamento: " . $e->getMessage());
        }
    }

    /**
     * Atualizar status do pagamento
     */
    public function atualizarStatus($id, $status) {
        try {
            $sql = "UPDATE pagamentos SET status = :status WHERE id = :id";
            
            Connection::getInstance()->executar($sql, [
                ':status' => $status,
                ':id' => $id
            ]);

            return true;
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar status: " . $e->getMessage());
        }
    }

    /**
     * Deletar pagamento
     */
    public function deletar($id) {
        try {
            $sql = "DELETE FROM pagamentos WHERE id = :id";
            Connection::getInstance()->executar($sql, [':id' => $id]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar pagamento: " . $e->getMessage());
        }
    }

    /**
     * Contar pagamentos por usuário
     */
    public function contarPorUsuario($usuario_id) {
        try {
            $sql = "SELECT COUNT(*) as total FROM pagamentos WHERE usuario_id = :usuario_id";
            $resultado = Connection::getInstance()->buscarUm($sql, [':usuario_id' => $usuario_id]);
            return $resultado['total'];
        } catch (Exception $e) {
            throw new Exception("Erro ao contar pagamentos: " . $e->getMessage());
        }
    }
}
?>
