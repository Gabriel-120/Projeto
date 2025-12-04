<?php
/**
 * Pagamento.php - Entidade de pagamento
 */

class Pagamento {
    private $id;
    private $usuario_id;
    private $plano;
    private $preco;
    private $data_pagamento;
    private $status;

    public function __construct($usuario_id = '', $plano = '', $preco = 0) {
        $this->usuario_id = $usuario_id;
        $this->plano = $plano;
        $this->preco = $preco;
        $this->status = 'pendente';
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

    public function getPlano() {
        return $this->plano;
    }

    public function setPlano($plano) {
        $this->plano = $plano;
    }

    public function getPreco() {
        return $this->preco;
    }

    public function setPreco($preco) {
        $this->preco = $preco;
    }

    public function getDataPagamento() {
        return $this->data_pagamento;
    }

    public function setDataPagamento($data_pagamento) {
        $this->data_pagamento = $data_pagamento;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }
}
?>
