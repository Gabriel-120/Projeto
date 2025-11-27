<?php
/**
 * RecuperacaoSenhaDAO.php - Acesso aos dados de recuperação de senha no MySQL
 */

require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/recuperacaoSenha.php';

class RecuperacaoSenhaDAO {
    private $conexao;

    public function __construct() {
        $this->conexao = Connection::getInstance()->getConexao();
    }

    /**
     * Criar novo token de recuperação
     */
    public function criar(RecuperacaoSenha $recuperacao) {
        try {
            $sql = "INSERT INTO recuperacao_senha (usuario_id, token, expiracao, utilizado) 
                    VALUES (:usuario_id, :token, :expiracao, :utilizado)";
            
            $id = Connection::getInstance()->inserir($sql, [
                ':usuario_id' => $recuperacao->getUsuarioId(),
                ':token' => $recuperacao->getToken(),
                ':expiracao' => $recuperacao->getExpiracao(),
                ':utilizado' => $recuperacao->getUtilizado() ? 1 : 0
            ]);

            return $id;
        } catch (Exception $e) {
            throw new Exception("Erro ao criar token de recuperação: " . $e->getMessage());
        }
    }

    /**
     * Buscar token de recuperação por token string
     */
    public function buscarPorToken($token) {
        try {
            $sql = "SELECT * FROM recuperacao_senha WHERE token = :token AND utilizado = 0";
            $resultado = Connection::getInstance()->buscarUm($sql, [':token' => $token]);
            
            if ($resultado) {
                $recuperacao = new RecuperacaoSenha($resultado['usuario_id'], $resultado['token']);
                $recuperacao->setId($resultado['id']);
                $recuperacao->setExpiracao($resultado['expiracao']);
                $recuperacao->setUtilizado((bool)$resultado['utilizado']);
                return $recuperacao;
            }
            return null;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar token: " . $e->getMessage());
        }
    }

    /**
     * Buscar tokens por usuário
     */
    public function buscarPorUsuario($usuario_id) {
        try {
            $sql = "SELECT * FROM recuperacao_senha WHERE usuario_id = :usuario_id 
                    ORDER BY id DESC";
            return Connection::getInstance()->buscarTodos($sql, [':usuario_id' => $usuario_id]);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar tokens: " . $e->getMessage());
        }
    }

    /**
     * Verificar se token é válido e não foi utilizado
     */
    public function tokenValido($token) {
        try {
            $recuperacao = $this->buscarPorToken($token);
            if ($recuperacao) {
                return $recuperacao->eValido();
            }
            return false;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Marcar token como utilizado
     */
    public function marcarUtilizado($token) {
        try {
            $sql = "UPDATE recuperacao_senha SET utilizado = 1 WHERE token = :token";
            Connection::getInstance()->executar($sql, [':token' => $token]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Erro ao marcar token como utilizado: " . $e->getMessage());
        }
    }

    /**
     * Deletar tokens expirados
     */
    public function deletarExpirados() {
        try {
            $sql = "DELETE FROM recuperacao_senha WHERE expiracao < NOW()";
            Connection::getInstance()->executar($sql);
            return true;
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar tokens expirados: " . $e->getMessage());
        }
    }

    /**
     * Deletar token específico
     */
    public function deletar($id) {
        try {
            $sql = "DELETE FROM recuperacao_senha WHERE id = :id";
            Connection::getInstance()->executar($sql, [':id' => $id]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar token: " . $e->getMessage());
        }
    }

    /**
     * Deletar todos os tokens de um usuário
     */
    public function deletarPorUsuario($usuario_id) {
        try {
            $sql = "DELETE FROM recuperacao_senha WHERE usuario_id = :usuario_id";
            Connection::getInstance()->executar($sql, [':usuario_id' => $usuario_id]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar tokens do usuário: " . $e->getMessage());
        }
    }
}
?>
