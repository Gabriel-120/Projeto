<?php
class Aviso
{
    private static ?PDO $pdo = null;

    private static function getPDO(): PDO
    {
        if (self::$pdo === null) {
            self::$pdo = Connect::conectar();
        }
        return self::$pdo;
    }
    public static function getTipoLabelsAvisos(): array
    {
        return [
            'Comunicado'     => 'Comunicado',
            'Promocao'       => 'Promoção',
            'Evento'         => 'Evento',
            'Manutencao'     => 'Manutenção',
            'MudancaHorario' => 'Mudança de Horário',
            'Novidade'       => 'Novidade',
            'DicaSaude'      => 'Dicas de Saúde',
            'AvisoSeguranca' => 'Aviso de Segurança',
        ];
    }

    public static function deleteExpired()
    {
        $pdo = self::getPDO();

        $sql  = "SELECT anexo_path FROM Avisos WHERE Expira < CURDATE()";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $avisosPath = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($avisosPath as $avisos) {
            if(empty($avisos['anexo_path'])) {
                continue;
            }
            $filename = __DIR__ . "/../../public/" . $avisos['anexo_path'];
            if (file_exists($filename)) {
                if (unlink($filename)) {
                    echo "Anexo excluído: $filename<br>";
                } else {
                    echo "Erro ao excluir: $filename<br>";
                }
            } else {
                echo "Arquivo não encontrado: $filename<br>";
            }
        }
        if (isset($filename)) {
            var_dump($filename);
        }

        $sql = "DELETE FROM Avisos WHERE Expira < CURDATE()";
        $pdo->exec($sql);

    }

    public static function getTipos()
    {
        $pdo  = self::getPDO();
        $sql  = "SHOW COLUMNS FROM Avisos LIKE 'tipo'";
        $stmt = $pdo->query($sql);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);

        $type = $row['Type'];

        $type = str_replace("enum(", "", $type);
        $type = str_replace(")", "", $type);

        $values = str_getcsv($type, ',', "'", '\\');

        return $values;
    }

    public static function createAviso(
        string $titulo,
        string $tipo,
        string $conteudo,
        int $idFuncionario,
        ?string $anexoPath = null
    ): bool {
        $pdo = self::getPDO();

        $sql = "INSERT INTO Avisos (titulo, tipo, conteudo, anexo_path, data_criacao, expira, id_funcionario)
                VALUES (?, ?, ?, ?, CURRENT_DATE, ?, ?)";

        $stmt = $pdo->prepare($sql);

        $expira = date('Y-m-d', strtotime('+1 month'));

        return $stmt->execute([
            $titulo,
            $tipo,
            $conteudo,
            $anexoPath,
            $expira,
            $idFuncionario,
        ]);
    }

    public static function getAllForAdmin(): array
    {
        $pdo = self::getPDO();
        $sql = "SELECT a.*, u.nome AS nome_funcionario
            FROM Avisos a
            JOIN Funcionarios f ON a.id_funcionario = f.id_funcionario
            JOIN Usuarios u ON f.id_usuario = u.id_usuario
            ORDER BY a.data_criacao DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById(int $id_alerta): ?array
    {
        $pdo = self::getPDO();
        $sql = "SELECT a.*, u.nome AS nome_funcionario
            FROM Avisos a
            JOIN Funcionarios f ON a.id_funcionario = f.id_funcionario
            JOIN Usuarios u ON f.id_usuario = u.id_usuario
            WHERE a.id_alerta = :id_alerta";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id_alerta', $id_alerta, PDO::PARAM_INT);
        $stmt->execute();

        $aviso = $stmt->fetch(PDO::FETCH_ASSOC);
        return $aviso ?: null;
    }

    public static function updateAviso(
        int $id_alerta,
        string $titulo,
        string $tipo,
        string $conteudo,
        string $expira,
        ?string $anexoPath = null
    ): bool {
        $pdo = self::getPDO();

        if ($anexoPath !== null) {

            $sql = "UPDATE Avisos
                SET titulo = ?, tipo = ?, conteudo = ?, expira = ?, anexo_path = ?
                WHERE id_alerta = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $titulo,
                $tipo,
                $conteudo,
                $expira,
                $anexoPath,
                $id_alerta,
            ]);
        } else {

            $sql = "UPDATE Avisos
                SET titulo = ?, tipo = ?, conteudo = ?, expira = ?
                WHERE id_alerta = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $titulo,
                $tipo,
                $conteudo,
                $expira,
                $id_alerta,
            ]);
        }
    }

    public static function deleteAviso(int $id_alerta): bool
    {
        $pdo  = self::getPDO();
        $sql  = "SELECT anexo_path FROM Avisos WHERE id_alerta = :id_alerta";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([":id_alerta" => $id_alerta]);

        $avisoPath = $stmt->fetch(PDO::FETCH_ASSOC);
        $filename  = __DIR__ . "/../../public/" . $avisoPath['anexo_path'];
        if (file_exists($filename)) {
            if (unlink($filename)) {
                echo "Anexo excluído: $filename<br>";
            } else {
                echo "Erro ao excluir: $filename<br>";
            }
        } else {
            echo "Arquivo não encontrado: $filename<br>";
        }
        $sql  = "DELETE FROM Avisos WHERE id_alerta = :id_alerta";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id_alerta', $id_alerta, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
