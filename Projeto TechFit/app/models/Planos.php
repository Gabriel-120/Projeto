<?php
class Planos
{
    private static ?PDO $pdo = null;

    private static function getPDO(): PDO
    {
        if (self::$pdo === null) {
            self::$pdo = Connect::conectar();
        }
        return self::$pdo;
    }

    public static function getAll(): array
    {
        $pdo = self::getPDO();
        $stmt = $pdo->query('SELECT id_plano, nome_plano, descricao_plano, preco, duracao FROM Planos ORDER BY id_plano DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create(string $nome, string $descricao, float $preco, int $duracao): bool
    {
        $pdo = self::getPDO();
        $stmt = $pdo->prepare('INSERT INTO Planos (nome_plano, descricao_plano, preco, duracao) VALUES (:nome, :descricao, :preco, :duracao)');
        return $stmt->execute([':nome'=>$nome, ':descricao'=>$descricao, ':preco'=>$preco, ':duracao'=>$duracao]);
    }

    public static function update(int $id, string $nome, string $descricao, float $preco, int $duracao): bool
    {
        $pdo = self::getPDO();
        $stmt = $pdo->prepare('UPDATE Planos SET nome_plano = :nome, descricao_plano = :descricao, preco = :preco, duracao = :duracao WHERE id_plano = :id');
        return $stmt->execute([':nome'=>$nome, ':descricao'=>$descricao, ':preco'=>$preco, ':duracao'=>$duracao, ':id'=>$id]);
    }

    public static function delete(int $id): bool
    {
        $pdo = self::getPDO();
        $stmt = $pdo->prepare('DELETE FROM Planos WHERE id_plano = :id');
        return $stmt->execute([':id'=>$id]);
    }
}
