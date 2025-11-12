<?php

require_once __DIR__ . '/cadastro.php';

class cadastroDAO {
    private $cadastro = [];
    private $arquivo = "cadastro.json";

    public function __construct() {
        if (file_exists($this->arquivo)) {
            $conteudo = file_get_contents($this->arquivo);

            $dados = json_decode($conteudo, true);

            if ($dados) {
                foreach ($dados as $id => $info) {
                    $this->cadastro[$id] = new cadastro(
                        $info['id'],
                        $info['nome'],
                        $info['email'],
                        $info['cpf'],
                        $info['data_nascimento'],
                        $info['senha']
                    );
                }
            }
        }
    }

    private function salvarEmArquivo() {
        $dados = [];

        foreach ($this->cadastro as $id => $cadastro) {
            $dados[$id] = [
                'id' => $cadastro->getId(),
                'nome' => $cadastro->getNome(),
                'email' => $cadastro->getEmail(),
                'cpf' => $cadastro->getCpf(),
                'data_nascimento' => $cadastro->getData_nascimento(),
                'senha' => $cadastro->getSenha(),
            ];
        }
        file_put_contents($this->arquivo, json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function CriarCadastro(cadastro $cadastro) {
        $this->cadastro[$cadastro->getId()] = $cadastro;
        $this->salvarEmArquivo();
    }

    public function atualizarCadastro($id, $Nnome, $Nemail, $Ncpf, $Ndata, $Nsenha) {
        if (isset($this->cadastro[$id])) {
            $this->cadastro[$id]->setNome($Nnome);
            $this->cadastro[$id]->setEmail($Nemail);
            $this->cadastro[$id]->setCpf($Ncpf);
            $this->cadastro[$id]->setData_nascimento($Ndata);
            $this->cadastro[$id]->setSenha($Nsenha);
        }
        $this->salvarEmArquivo();
    }

    public function excluirCadastro($id) {
        unset($this->cadastro[$id]);
        $this->salvarEmArquivo();
    }

    public function lerCadastro() {
        return $this->cadastro;
    }
}