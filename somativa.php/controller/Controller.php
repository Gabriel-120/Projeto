<?php 
require_once __DIR__ . '/../model/Livros.php';
require_once __DIR__ . '/../model/LivrosDAO.php';

class LivrosController {
    private $dao;

    public function __construct() {
        $this->dao = new LivrosDAO();
    }

    public function criar($titulo, $autor, $ano, $genero, $quantidade) {
        // Verifica se já existe um livro com o mesmo título
        $existe = $this->dao->buscarPorTitulo($titulo);
        if ($existe) {
            // retorna false para indicar falha por duplicidade
            return false;
        }
        $livros = new Livros($titulo, $autor, $ano, $genero, $quantidade);
        $this->dao->criarLivros($livros);
        return true;
    }

    public function ler() {
        return $this->dao->lerLivros();
    }

    public function atualizar($tituloOriginal, $N_titulo, $N_autor, $N_ano, $N_genero, $N_quantidade) {
        // Se o título foi alterado, garantir que o novo título não exista já
        if ($N_titulo !== $tituloOriginal) {
            $existe = $this->dao->buscarPorTitulo($N_titulo);
            if ($existe) {
                return false;
            }
        }
        $this->dao->atualizarLivros($tituloOriginal, $N_titulo, $N_autor, $N_ano, $N_genero, $N_quantidade);
        return true;
    }

    public function buscarPorTitulo($titulo) {
       return $this->dao->buscarPorTitulo($titulo);
    }

    public function deletar($titulo) {
        $this->dao->excluirLivros($titulo);
    }
}