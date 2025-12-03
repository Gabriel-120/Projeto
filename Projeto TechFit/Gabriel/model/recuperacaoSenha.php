<?php
/**
 * RecuperacaoSenha.php - Entidade de recuperação de senha
 */

class RecuperacaoSenha {
    private $id;
    private $usuario_id;
    private $token;
    private $expiracao;
    private $utilizado;

    public function __construct($usuario_id = '', $token = '') {
        $this->usuario_id = $usuario_id;
        $this->token = $token;
        $this->utilizado = false;
        // Token expira em 1 hora
        $this->expiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUsuarioId() {
        return $this->usuario_id;
    }

    public function setUsuarioId($usuario_id) {
        $this->usuario_id = $usuario_id;
    }

    public function getToken() {
        return $this->token;
    }

    public function setToken($token) {
        $this->token = $token;
    }

    public function getExpiracao() {
        return $this->expiracao;
    }

    public function setExpiracao($expiracao) {
        $this->expiracao = $expiracao;
    }

    public function getUtilizado() {
        return $this->utilizado;
    }

    public function setUtilizado($utilizado) {
        $this->utilizado = $utilizado;
    }

    /**
     * Gerar token aleatório único
     */
    public static function gerarToken() {
        return bin2hex(random_bytes(32));
    }

    /**
     * Verificar se token está expirado
     */
    public function estaExpirado() {
        $agora = new DateTime();
        $data_expiracao = new DateTime($this->expiracao);
        return $agora > $data_expiracao;
    }

    /**
     * Verificar se token é válido (não expirado e não utilizado)
     */
    public function eValido() {
        return !$this->estaExpirado() && !$this->utilizado;
    }
}
?>
