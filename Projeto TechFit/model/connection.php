<?php
/**
 * Connection.php - Configuração e conexão com banco de dados MySQL
 * Usando PDO para maior segurança
 */

class Connection {
    private static $instance = null;
    private $conexao;

    private function __construct() {
        $this->conectar();
    }

    /**
     * Singleton - Obter instância única da conexão
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Conectar ao banco de dados
     */
    private function conectar() {
        try {
            $host = 'localhost';
            $db = 'techfit';
            $user = 'root';
            $password = 'senaisp';

            $this->conexao = new PDO(
                "mysql:host={$host};dbname={$db};charset=utf8mb4",
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die("Erro ao conectar ao banco de dados: " . $e->getMessage());
        }
    }

    /**
     * Obter conexão PDO
     */
    public function getConexao() {
        return $this->conexao;
    }

    /**
     * Executar query com preparação (segura)
     */
    public function executar($sql, $parametros = []) {
        try {
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute($parametros);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Erro ao executar query: " . $e->getMessage());
        }
    }

    /**
     * Buscar um resultado
     */
    public function buscarUm($sql, $parametros = []) {
        try {
            $stmt = $this->executar($sql, $parametros);
            return $stmt->fetch();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Buscar múltiplos resultados
     */
    public function buscarTodos($sql, $parametros = []) {
        try {
            $stmt = $this->executar($sql, $parametros);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Inserir/Atualizar/Deletar
     */
    public function inserir($sql, $parametros = []) {
        try {
            $stmt = $this->executar($sql, $parametros);
            return $this->conexao->lastInsertId();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Iniciar transação
     */
    public function iniciarTransacao() {
        $this->conexao->beginTransaction();
    }

    /**
     * Confirmar transação
     */
    public function confirmar() {
        $this->conexao->commit();
    }

    /**
     * Desfazer transação
     */
    public function desfazer() {
        $this->conexao->rollBack();
    }

    /**
     * Verificar quais tabelas existem no banco
     * Retorna array com informações das tabelas do script_TechFit.sql
     */
    public function verificarTabelas() {
        try {
            $sql = "SHOW TABLES";
            $stmt = $this->conexao->query($sql);
            $tabelas = [];
            
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $tabelas[] = $row[0];
            }
            
            return $tabelas;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar tabelas: " . $e->getMessage());
        }
    }

    /**
     * Verificar se a tabela existe
     */
    public function tabelaExiste($nomeTabelar) {
        try {
            // Alguns comandos SHOW ... LIKE não aceitam parâmetros em todas as versões/engines.
            // Validar nome da tabela para evitar SQL injection e montar a query segura.
            if (!preg_match('/^[A-Za-z0-9_]+$/', $nomeTabelar)) {
                throw new Exception("Nome de tabela inválido: " . $nomeTabelar);
            }

            $quoted = $this->conexao->quote($nomeTabelar);
            $sql = "SHOW TABLES LIKE " . $quoted;
            $stmt = $this->conexao->query($sql);
            $row = $stmt->fetch(PDO::FETCH_NUM);
            return $row !== false;
        } catch (PDOException $e) {
            throw new Exception("Erro ao verificar tabela: " . $e->getMessage());
        }
    }

    /**
     * Obter informações das colunas de uma tabela
     */
    public function obterColunas($nomeTabela) {
        try {
            // Validar nome da tabela antes de interpolar
            if (!preg_match('/^[A-Za-z0-9_]+$/', $nomeTabela)) {
                throw new Exception("Nome de tabela inválido: " . $nomeTabela);
            }

            $sql = "DESCRIBE `" . $nomeTabela . "`";
            $stmt = $this->conexao->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Erro ao obter colunas: " . $e->getMessage());
        }
    }

    /**
     * Fechar conexão
     */
    public function fechar() {
        $this->conexao = null;
    }
}
?>
