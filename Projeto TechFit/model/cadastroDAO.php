<?php
/**
 * CadastroDAO.php - Acesso aos dados de usuários no MySQL
 */

require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/cadastro.php';

class CadastroDAO {
    private $conexao;

    public function __construct() {
        $this->conexao = Connection::getInstance()->getConexao();
    }

    /**
     * Criar novo usuário no banco de dados
     */
    public function criar(Cadastro $cadastro) {
        try {
            // Verificar se a tabela Usuarios existe
            if (!Connection::getInstance()->tabelaExiste('Usuarios')) {
                throw new Exception("Tabela Usuarios não existe. Execute o script_TechFit.sql primeiro.");
            }

            // Inserir na tabela Usuarios: incluir nome, cpf e data_nascimento (precisa existir na tabela)
            $sql = "INSERT INTO Usuarios (nome, email, cpf, data_nascimento, senha_hash, tipo, Avatar) 
                    VALUES (:nome, :email, :cpf, :data_nascimento, :senha_hash, :tipo, :avatar)";
            
            $stmt = Connection::getInstance()->executar($sql, [
                ':nome' => $cadastro->getNome(),
                ':email' => $cadastro->getEmail(),
                ':cpf' => $cadastro->getCpf(),
                ':data_nascimento' => $cadastro->getDataNascimento(),
                ':senha_hash' => password_hash($cadastro->getSenha(), PASSWORD_DEFAULT),
                ':tipo' => 'usuario',
                ':avatar' => 'public/images/pfp/placeholder.png'
            ]);

            return Connection::getInstance()->getConexao()->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Erro ao criar usuário: " . $e->getMessage());
        }
    }

    /**
     * Buscar usuário por ID
     */
    public function lerPorId($id) {
        try {
            $sql = "SELECT * FROM Usuarios WHERE id_usuario = :id";
            $resultado = Connection::getInstance()->buscarUm($sql, [':id' => $id]);
            
            if ($resultado) {
                $cadastro = new Cadastro(
                    isset($resultado['nome']) ? $resultado['nome'] : '',
                    isset($resultado['email']) ? $resultado['email'] : '',
                    isset($resultado['cpf']) ? $resultado['cpf'] : '',
                    isset($resultado['data_nascimento']) ? $resultado['data_nascimento'] : ''
                );
                $cadastro->setId($resultado['id_usuario']);
                return $cadastro;
            }
            return null;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar usuário: " . $e->getMessage());
        }
    }

    /**
     * Buscar usuário por email
     */
    public function buscarPorEmail($email) {
        try {
            $sql = "SELECT * FROM Usuarios WHERE email = :email";
            $resultado = Connection::getInstance()->buscarUm($sql, [':email' => $email]);
            return $resultado;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar por email: " . $e->getMessage());
        }
    }

    /**
     * Buscar usuário por CPF
     */
    public function buscarPorCpf($cpf) {
        try {
            // Verificar se a coluna 'cpf' existe na tabela Usuarios
            $colunas = Connection::getInstance()->obterColunas('Usuarios');
            $hasCpf = false;
            foreach ($colunas as $col) {
                if (isset($col['Field']) && strtolower($col['Field']) === 'cpf') {
                    $hasCpf = true;
                    break;
                }
            }

            if (!$hasCpf) {
                // Coluna CPF não existe: retornar null para indicar que não há correspondência
                return null;
            }

            $sql = "SELECT * FROM Usuarios WHERE cpf = :cpf";
            $resultado = Connection::getInstance()->buscarUm($sql, [':cpf' => $cpf]);
            return $resultado;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar por CPF: " . $e->getMessage());
        }
    }

    /**
     * Buscar usuário por nome
     */
    public function buscarPorNome($nome) {
        try {
            // Verificar se a coluna 'nome' existe na tabela Usuarios
            $colunas = Connection::getInstance()->obterColunas('Usuarios');
            $hasNome = false;
            foreach ($colunas as $col) {
                if (isset($col['Field']) && strtolower($col['Field']) === 'nome') {
                    $hasNome = true;
                    break;
                }
            }

            if (!$hasNome) {
                // Coluna nome não existe: retornar null para indicar que não há correspondência
                return null;
            }

            $sql = "SELECT * FROM Usuarios WHERE nome = :nome";
            $resultado = Connection::getInstance()->buscarUm($sql, [':nome' => $nome]);
            return $resultado;
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar por nome: " . $e->getMessage());
        }
    }

    /**
     * Buscar todos os usuários
     */
    public function lerTodos() {
        try {
            $sql = "SELECT * FROM Usuarios ORDER BY id_usuario DESC";
            return Connection::getInstance()->buscarTodos($sql);
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar usuários: " . $e->getMessage());
        }
    }

    /**
     * Atualizar usuário
     */
    public function atualizar(Cadastro $cadastro) {
        try {
            $sql = "UPDATE Usuarios SET nome = :nome, email = :email, cpf = :cpf, data_nascimento = :data_nascimento WHERE id_usuario = :id";
            
            Connection::getInstance()->executar($sql, [
                ':nome' => $cadastro->getNome(),
                ':email' => $cadastro->getEmail(),
                ':cpf' => $cadastro->getCpf(),
                ':data_nascimento' => $cadastro->getDataNascimento(),
                ':id' => $cadastro->getId()
            ]);

            return true;
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar usuário: " . $e->getMessage());
        }
    }

    /**
     * Atualizar senha
     */
    public function atualizarSenha($usuario_id, $nova_senha) {
        try {
            $senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $sql = "UPDATE Usuarios SET senha_hash = :senha WHERE id_usuario = :id";
            
            Connection::getInstance()->executar($sql, [
                ':senha' => $senha_hash,
                ':id' => $usuario_id
            ]);

            return true;
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar senha: " . $e->getMessage());
        }
    }

    /**
     * Deletar usuário
     */
    public function deletar($id) {
        try {
            $sql = "DELETE FROM Usuarios WHERE id_usuario = :id";
            Connection::getInstance()->executar($sql, [':id' => $id]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Erro ao deletar usuário: " . $e->getMessage());
        }
    }

    /**
     * Verificar se email existe
     */
    public function emailExists($email) {
        try {
            $resultado = $this->buscarPorEmail($email);
            return ($resultado !== false && $resultado !== null && !empty($resultado));
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verificar se CPF existe
     */
    public function cpfExists($cpf) {
        try {
            $resultado = $this->buscarPorCpf($cpf);
            return ($resultado !== false && $resultado !== null && !empty($resultado));
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Verificar se nome existe
     */
    public function nomeExists($nome) {
        try {
            $resultado = $this->buscarPorNome($nome);
            return ($resultado !== false && $resultado !== null && !empty($resultado));
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Autenticar usuário (buscar por email e verificar senha)
     */
    public function autenticar($email, $senha) {
        try {
            $usuario = $this->buscarPorEmail($email);
            if ($usuario && password_verify($senha, $usuario['senha_hash'])) {
                return $usuario;
            }
            return null;
        } catch (Exception $e) {
            throw new Exception("Erro ao autenticar: " . $e->getMessage());
        }
    }
}