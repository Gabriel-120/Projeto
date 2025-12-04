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

    /**
     * Cria um novo funcionário
     * @param array $dados Dados do funcionário (id_usuario, cpf_funcionario, cargo, etc)
     * @return int ID do funcionário criado
     */
    public static function criarFuncionario(array $dados): int
    {
        $pdo = self::getPDO();
        
        $campos = array_keys($dados);
        $placeholders = array_map(fn($c) => ":$c", $campos);
        
        $sql = "INSERT INTO Funcionarios (" . implode(", ", $campos) . ") 
                VALUES (" . implode(", ", $placeholders) . ")";
        
        $stmt = $pdo->prepare($sql);
        
        foreach ($dados as $chave => $valor) {
            $stmt->bindValue(":$chave", $valor);
        }
        
        $stmt->execute();
        return (int)$pdo->lastInsertId();
    }
}
