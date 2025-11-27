<?php

require_once 'Livros.php';
require_once 'Connection.php';

class LivrosDAO
{
    private $conn;

    public function __construct()
    {
        $this->conn = Connetion::getInstance();

        $this->conn->exec("CREATE TABLE IF NOT EXISTS Livros (Id INT AUTO_INCREMENT PRIMARY KEY, Titulo VARCHAR(200), Autor VARCHAR(150), Ano INT, Genero VARCHAR(100), Quantidade INT);");
    }

    public function criarLivros(Livros $livros) {
        $stmt = $this->conn->prepare("INSERT INTO Livros (Titulo, Autor, Ano, Genero, Quantidade) VALUES (:titulo, :autor, :ano, :genero, :quantidade)");
        $stmt->execute([
            ':titulo' => $livros->getTitulo(), 
            ':autor' => $livros->getAutor(),
            ':ano' => $livros->getAno_publicacao(),
            ':genero' => $livros->getGenero(),
            ':quantidade' => $livros->getQuantidade()
        ]);
    }


    public function lerLivros() {
        $stmt = $this->conn->query("SELECT * FROM Livros ORDER BY Titulo");
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new Livros(
                $row['Titulo'],
                $row['Autor'],
                $row['Ano'],
                $row['Genero'],
            $row['Quantidade']
            );
        }
        return $result; 
    }


    public function atualizarLivros($tituloOriginal, $N_titulo, $N_autor, $N_ano, $N_genero, $N_quantidade) {
        $stmt = $this->conn->prepare("UPDATE Livros SET Titulo = :titulo, Autor = :autor, Ano = :ano, Genero = :genero, Quantidade = :quantidade WHERE Titulo = :tituloOriginal");
        $stmt->execute([
            ':titulo' => $N_titulo,
            ':autor' => $N_autor,
            ':ano' => $N_ano,
            ':genero' => $N_genero,
            ':quantidade' => $N_quantidade,
            ':tituloOriginal' => $tituloOriginal
        ]);
    }

    public function excluirLivros($titulo) {
        $stmt = $this->conn->prepare("DELETE FROM Livros WHERE Titulo = :titulo");
        $stmt->execute([':titulo' => $titulo]);
    }

    public function buscarPorTitulo($titulo){
        $stmt = $this->conn->prepare("SELECT * FROM Livros WHERE Titulo = :titulo LIMIT 1");
        $stmt->execute([':titulo' => $titulo]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new Livros(
                $row['Titulo'],
                $row['Autor'],
                $row['Ano'],
                $row['Genero'],
                $row['Quantidade']
            );
        }
        return null;
    }
}
