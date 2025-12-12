<?php
require_once __DIR__ . '/../app/models/Connect.php';

echo "Running DB check...\n";
try {
    $pdo = Connect::conectar();
    $dbName = $pdo->query('select database()')->fetchColumn();
    echo "Connected to database: " . ($dbName ?: '(unknown)') . "\n\n";

    // Check if table exists
    $tbl = 'Usuarios';
    $stmt = $pdo->prepare("SHOW TABLES LIKE :tbl");
    $stmt->execute([':tbl' => $tbl]);
    $exists = (bool) $stmt->fetchColumn();

    if (! $exists) {
        echo "Table '$tbl' does not exist in database.\n";
        exit(1);
    }

    echo "Table '$tbl' exists. Columns:\n";
    $desc = $pdo->query("DESCRIBE $tbl")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($desc as $col) {
        echo sprintf("- %s: %s %s %s\n", $col['Field'], $col['Type'], $col['Null'] === 'NO' ? 'NOT NULL' : 'NULL', $col['Key'] ? "Key={$col['Key']}" : '');
    }

    // quick check for cpf column
    $hasCpf = false;
    foreach ($desc as $col) {
        if (strtolower($col['Field']) === 'cpf') { $hasCpf = true; break; }
    }

    if ($hasCpf) {
        echo "\nColumn 'cpf' is present.\n";
        exit(0);
    } else {
        echo "\nColumn 'cpf' is NOT present. That explains the 'Unknown column \'cpf\'' error.\n";
        echo "You should import the project's SQL schema (see db/Fisico techfit final.sql) or add the column manually.\n";
        exit(2);
    }

} catch (Exception $e) {
    echo "Error connecting or querying the database: " . $e->getMessage() . "\n";
    exit(3);
}

