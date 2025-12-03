<?php

class Connect {
    private static $pdo = null;

    public static function conectar() {
        if (self::$pdo === null) {
            // Verifica se o arquivo .env existe
            $envFile = __DIR__ . "/../config/.env";
            if (!file_exists($envFile)) {
                die("Erro: Arquivo .env não encontrado.");
            }

            // Carrega as variáveis de ambiente
            $env = parse_ini_file($envFile);
            if (!$env || !isset($env['DB_HOST'], $env['DB_NAME'], $env['DB_USER'], $env['DB_PASSWORD'], $env['DB_CHARSET'])) {
                die("Erro: Variáveis de ambiente faltando no arquivo .env.");
            }

            // Monta o DSN para PDO
            $dsn = 'mysql:host=' . $env['DB_HOST'] . ';dbname=' . $env['DB_NAME'] . ';charset=' . $env['DB_CHARSET'];
            
            try {
                self::$pdo = new PDO($dsn, $env['DB_USER'], $env["DB_PASSWORD"]);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Erro ao conectar ao banco de dados: " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
