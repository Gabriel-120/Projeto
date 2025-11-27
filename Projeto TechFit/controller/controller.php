<?php

require_once __DIR__ . '/../model/cadastro.php';
require_once __DIR__ . '/../model/cadastroDAO.php';
require_once __DIR__ . '/../model/pagamento.php';
require_once __DIR__ . '/../model/pagamentoDAO.php';
require_once __DIR__ . '/../model/recuperacaoSenha.php';
require_once __DIR__ . '/../model/recuperacaoSenhaDAO.php';
require_once __DIR__ . '/../model/connection.php';

class CadastroController {
    private $cadastroDAO;
    private $pagamentoDAO;
    private $recuperacaoDAO;

    public function __construct() {
        $this->cadastroDAO = new CadastroDAO();
        $this->pagamentoDAO = new PagamentoDAO();
        $this->recuperacaoDAO = new RecuperacaoSenhaDAO();
    }

    /**
     * Criar novo usuário com validações
     */
    public function criar($nome, $email, $cpf, $data_nascimento, $senha, $confirma_senha) {
        try {
            // Validar preenchimento
            if (empty($nome) || empty($email) || empty($cpf) || empty($data_nascimento) || empty($senha)) {
                return ['sucesso' => false, 'erro' => 'Todos os campos são obrigatórios'];
            }

            // Validar confirmação de senha
            if ($senha !== $confirma_senha) {
                return ['sucesso' => false, 'erro' => 'As senhas não conferem'];
            }

            // Validar formato de email
            if (!Cadastro::validarEmail($email)) {
                return ['sucesso' => false, 'erro' => 'Email inválido'];
            }

            // Validar formato de CPF
            if (!Cadastro::validarCPF($cpf)) {
                return ['sucesso' => false, 'erro' => 'CPF inválido'];
            }

            // Validar data
            if (!Cadastro::validarData($data_nascimento)) {
                return ['sucesso' => false, 'erro' => 'Data inválida. Use o formato YYYY-MM-DD'];
            }

            // Verificar se email, CPF ou nome já existem
            if ($this->cadastroDAO->emailExists($email)) {
                return ['sucesso' => false, 'erro' => 'Este email já foi registrado'];
            }

            if ($this->cadastroDAO->cpfExists($cpf)) {
                return ['sucesso' => false, 'erro' => 'Este CPF já foi registrado'];
            }

            if ($this->cadastroDAO->nomeExists($nome)) {
                return ['sucesso' => false, 'erro' => 'Este nome de usuário já existe'];
            }

            // Criar novo cadastro
            $cadastro = new Cadastro($nome, $email, $cpf, $data_nascimento, $senha);
            $id = $this->cadastroDAO->criar($cadastro);

            return ['sucesso' => true, 'mensagem' => 'Cadastro realizado com sucesso', 'usuario_id' => $id];

        } catch (Exception $e) {
            return ['sucesso' => false, 'erro' => 'Erro ao criar cadastro: ' . $e->getMessage()];
        }
    }

    /**
     * Autenticar usuário
     */
    public function autenticar($email, $senha) {
        try {
            if (empty($email) || empty($senha)) {
                return ['sucesso' => false, 'erro' => 'Email e senha são obrigatórios'];
            }

            $usuario = $this->cadastroDAO->autenticar($email, $senha);
            
            if ($usuario) {
                return ['sucesso' => true, 'usuario' => $usuario];
            } else {
                return ['sucesso' => false, 'erro' => 'Email ou senha incorretos'];
            }

        } catch (Exception $e) {
            return ['sucesso' => false, 'erro' => 'Erro ao autenticar: ' . $e->getMessage()];
        }
    }

    /**
     * Solicitar recuperação de senha
     */
    public function solicitarRecuperacao($email, $cpf) {
        try {
            if (empty($email) || empty($cpf)) {
                return ['sucesso' => false, 'erro' => 'Email e CPF são obrigatórios'];
            }

            // Buscar usuário por email e CPF
            $usuario = $this->cadastroDAO->buscarPorEmail($email);
            
            if (!$usuario) {
                return ['sucesso' => false, 'erro' => 'Email não encontrado'];
            }

            if (!isset($usuario['cpf']) || $usuario['cpf'] !== $cpf) {
                return ['sucesso' => false, 'erro' => 'CPF não corresponde ao email informado'];
            }

            // Gerar token
            $token = RecuperacaoSenha::gerarToken();
            $recuperacao = new RecuperacaoSenha($usuario['id_usuario'], $token);
            $recuperacao_id = $this->recuperacaoDAO->criar($recuperacao);

            return ['sucesso' => true, 'token' => $token, 'usuario_id' => $usuario['id_usuario']];

        } catch (Exception $e) {
            return ['sucesso' => false, 'erro' => 'Erro ao solicitar recuperação: ' . $e->getMessage()];
        }
    }

    /**
     * Verificar se token de recuperação é válido
     */
    public function verificarToken($token) {
        try {
            if (empty($token)) {
                return ['sucesso' => false, 'erro' => 'Token inválido'];
            }

            $valido = $this->recuperacaoDAO->tokenValido($token);
            
            if ($valido) {
                return ['sucesso' => true];
            } else {
                return ['sucesso' => false, 'erro' => 'Token inválido ou expirado'];
            }

        } catch (Exception $e) {
            return ['sucesso' => false, 'erro' => 'Erro ao verificar token: ' . $e->getMessage()];
        }
    }

    /**
     * Redefinir senha
     */
    public function redefinirSenha($token, $nova_senha, $confirma_senha) {
        try {
            if (empty($token) || empty($nova_senha)) {
                return ['sucesso' => false, 'erro' => 'Token e nova senha são obrigatórios'];
            }

            if ($nova_senha !== $confirma_senha) {
                return ['sucesso' => false, 'erro' => 'As senhas não conferem'];
            }

            if (strlen($nova_senha) < 6) {
                return ['sucesso' => false, 'erro' => 'A senha deve ter no mínimo 6 caracteres'];
            }

            // Buscar token
            $recuperacao = $this->recuperacaoDAO->buscarPorToken($token);
            
            if (!$recuperacao || !$recuperacao->eValido()) {
                return ['sucesso' => false, 'erro' => 'Token inválido ou expirado'];
            }

            // Atualizar senha
            $this->cadastroDAO->atualizarSenha($recuperacao->getUsuarioId(), $nova_senha);
            
            // Marcar token como utilizado
            $this->recuperacaoDAO->marcarUtilizado($token);

            return ['sucesso' => true, 'mensagem' => 'Senha redefinida com sucesso'];

        } catch (Exception $e) {
            return ['sucesso' => false, 'erro' => 'Erro ao redefinir senha: ' . $e->getMessage()];
        }
    }

    /**
     * Registrar pagamento
     */
    public function registrarPagamento($usuario_id, $plano, $preco) {
        try {
            if (empty($usuario_id) || empty($plano) || empty($preco)) {
                return ['sucesso' => false, 'erro' => 'Dados de pagamento incompletos'];
            }

            $pagamento = new Pagamento($usuario_id, $plano, $preco);
            $pagamento->setStatus('confirmado');
            $id = $this->pagamentoDAO->criar($pagamento);

            return ['sucesso' => true, 'mensagem' => 'Pagamento registrado com sucesso', 'pagamento_id' => $id];

        } catch (Exception $e) {
            return ['sucesso' => false, 'erro' => 'Erro ao registrar pagamento: ' . $e->getMessage()];
        }
    }

    /**
     * Obter histórico de pagamentos do usuário
     */
    public function obterPagamentos($usuario_id) {
        try {
            if (empty($usuario_id)) {
                return ['sucesso' => false, 'erro' => 'Usuário inválido'];
            }

            $pagamentos = $this->pagamentoDAO->buscarPorUsuario($usuario_id);
            return ['sucesso' => true, 'pagamentos' => $pagamentos];

        } catch (Exception $e) {
            return ['sucesso' => false, 'erro' => 'Erro ao obter pagamentos: ' . $e->getMessage()];
        }
    }

    /**
     * Buscar usuário por ID
     */
    public function buscarPorId($id) {
        try {
            $usuario = $this->cadastroDAO->buscarPorEmail(''); // Implementar buscar direto por ID
            return ['sucesso' => true, 'usuario' => $usuario];
        } catch (Exception $e) {
            return ['sucesso' => false, 'erro' => 'Erro ao buscar usuário: ' . $e->getMessage()];
        }
    }

    /**
     * Buscar todos os usuários
     */
    public function buscarTodos() {
        try {
            $usuarios = $this->cadastroDAO->lerTodos();
            return ['sucesso' => true, 'usuarios' => $usuarios];
        } catch (Exception $e) {
            return ['sucesso' => false, 'erro' => 'Erro ao buscar usuários: ' . $e->getMessage()];
        }
    }
}