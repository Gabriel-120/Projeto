<?php
class Funcionario
{
    private static ?PDO $pdo = null;

    private static function getPDO(): PDO
    {
        if (self::$pdo === null) {
            self::$pdo = Connect::conectar();
        }
        return self::$pdo;
    }

    public static function getByUsuarioId(int $id_usuario): ?array
    {
        $pdo = self::getPDO();
        $sql = "SELECT * FROM Funcionarios WHERE id_usuario = :id_usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $func = $stmt->fetch(PDO::FETCH_ASSOC);

        return $func ?: null;
    }
}
