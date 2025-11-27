<?php

class Livros {
    private $titulo;
    private $autor;
    private $ano_publicacao;
    private $genero;
    private $quantidade;
    
    public function __construct($titulo, $autor, $ano_publicacao, $genero, $quantidade) {
        $this->titulo = $titulo;
        $this->autor = $autor;
        $this->ano_publicacao = $ano_publicacao;
        $this->genero = $genero;
        $this->quantidade = $quantidade;
    }
    
    public function getTitulo() {
        return $this->titulo;
    }
    public function getAutor() {
        return $this->autor;
    }
    public function getAno_publicacao() {
        return $this->ano_publicacao;
    }
    public function getGenero() {
        return $this->genero;
    }
    public function getQuantidade() {
        return $this->quantidade;
    }


    public function setTitulo($N_titulo) {
        $this->titulo = $N_titulo;
    }
    public function setAutor($N_autor) {
        $this->autor = $N_autor;
    }
    public function setAno_publicacao($N_ano) {
        $this->ano_publicacao = $N_ano;
    }
    public function setGenero($N_genero) {
        $this->genero = $N_genero;
    }
    public function setQuantidade($N_quantidade) {
        $this->quantidade = $N_quantidade;
    }
}