<?php 

class cadastro {
    private $id;
    private $nome;
    private $email;
    private $cpf;
    private $data_nascimento;
    private $senha;

    public function __construct($id, $nome, $email, $cpf, $data_nascimento, $senha) {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->cpf = $cpf;
        $this->data_nascimento = $data_nascimento;
        $this->senha = $senha;
    }

    public function getId() {
        return $this->id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getCpf() {
        return $this->cpf;
    }

    public function getData_nascimento() {
        return $this->data_nascimento;
    }

    public function getSenha() {
        return $this->senha;
    }


    public function setId($Nid) {
        $this->id = $Nid;
    }

    public function setNome($Nnome) {
        $this->nome = $Nnome;
    }

    public function setEmail($Nemail) {
        $this->email = $Nemail;
    }

    public function setCpf($Ncpf) {
        $this->cpf = $Ncpf;
    }

    public function setData_nascimento($nascimento) {
        $this->data_nascimento = $nascimento;
    }

    public function setSenha($Nsenha) {
        $this->senha = $Nsenha;
    }
}