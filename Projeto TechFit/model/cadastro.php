<?php
/**
 * Cadastro.php - Entidade de usuário
 */

class Cadastro {
    private $id;
    private $nome;
    private $email;
    private $cpf;
    private $data_nascimento;
    private $senha;
    private $data_criacao;

    public function __construct($nome = '', $email = '', $cpf = '', $data_nascimento = '', $senha = '') {
        $this->nome = $nome;
        $this->email = $email;
        $this->cpf = $cpf;
        $this->data_nascimento = $data_nascimento;
        $this->senha = $senha;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function setNome($nome) {
        $this->nome = $nome;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getCpf() {
        return $this->cpf;
    }

    public function setCpf($cpf) {
        $this->cpf = $cpf;
    }

    public function getDataNascimento() {
        return $this->data_nascimento;
    }

    public function setDataNascimento($data_nascimento) {
        $this->data_nascimento = $data_nascimento;
    }

    public function getSenha() {
        return $this->senha;
    }

    public function setSenha($senha) {
        $this->senha = password_hash($senha, PASSWORD_DEFAULT);
    }

    public function getDataCriacao() {
        return $this->data_criacao;
    }

    public function setDataCriacao($data_criacao) {
        $this->data_criacao = $data_criacao;
    }

    /**
     * Validar senha contra hash armazenado
     */
    public function verificarSenha($senha_plain) {
        return password_verify($senha_plain, $this->senha);
    }

    /**
     * Validar formato de CPF (xxx.xxx.xxx-xx)
     */
    public static function validarCPF($cpf) {
        // Remove pontos e hífen
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        // Verifica se tem 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        // Valida primeiro dígito verificador
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += intval($cpf[$i]) * (10 - $i);
        }
        $resto = $soma % 11;
        $digito1 = ($resto < 2) ? 0 : (11 - $resto);

        if ($digito1 != $cpf[9]) {
            return false;
        }

        // Valida segundo dígito verificador
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += intval($cpf[$i]) * (11 - $i);
        }
        $resto = $soma % 11;
        $digito2 = ($resto < 2) ? 0 : (11 - $resto);

        return ($digito2 == $cpf[10]);
    }

    /**
     * Validar formato de email
     */
    public static function validarEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validar data em formato YYYY-MM-DD
     */
    public static function validarData($data) {
        $d = DateTime::createFromFormat('Y-m-d', $data);
        return $d && $d->format('Y-m-d') === $data;
    }
}