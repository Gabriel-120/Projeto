<?php 

require_once __DIR__ . '/../model/cadastro.php';
require_once __DIR__ . '/../model/cadastroDAO.php';

class cadastroController {
    private $dao;

    public function __construct() {
        $this->dao = new cadastroDAO();
    }

    public function ler() {
        return $this->dao->lerCadastro();
    }

    public function criar($nome, $email, $cpf, $data, $senha) {
        $cadastros = $this->dao->lerCadastro();
        
        // Gerar próximo ID
        if (empty($cadastros)) {
            $id = 1;
        } else {
            $ultimo = end($cadastros);
            $id = $ultimo->getId() + 1;
        }
        
        $cadastro = new cadastro($id, $nome, $email, $cpf, $data, $senha);
        $this->dao->CriarCadastro($cadastro);
        return $id;
    }

    public function atualizar($id, $nome, $email, $cpf, $data, $senha) {
        $this->dao->atualizarCadastro($id, $nome, $email, $cpf, $data, $senha);
    }

    public function deletar($id) {
        $this->dao->excluirCadastro($id);
    }
}