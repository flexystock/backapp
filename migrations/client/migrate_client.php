<?php
$mainPdo = new PDO('mysql:host=docker-symfony-dbMain;dbname=docker_symfony_databaseMain', 'user', 'password');

// Obtener lista de clientes con sus bases de datos, hosts, usuarios y contraseñas
$clients = $mainPdo->query("SELECT databaseName, host, username, password FROM client")->fetchAll(PDO::FETCH_ASSOC);

function applyMigrations($pdo, $basePath) {
    if ($basePath === false || !is_dir($basePath)) {
        echo "Invalid migrations path: $basePath\n";
        return;
    }

    // Crear la tabla de versiones de migraciones si no existe
    $pdo->exec("CREATE TABLE IF NOT EXISTS migrations_version (
        id INT AUTO_INCREMENT PRIMARY KEY,
        version VARCHAR(255) NOT NULL,
        script VARCHAR(255) NOT NULL,
        executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Obtener la última versión ejecutada
    $lastVersion = $pdo->query("SELECT MAX(version) FROM migrations_version")->fetchColumn();
    if ($lastVersion === false) {
        $lastVersion = '000';
    }

    foreach (scandir($basePath) as $versionDir) {
        if ($versionDir === '.' || $versionDir === '..') continue;

        // Comparar con la última versión ejecutada
        if ($versionDir <= $lastVersion) {
            echo "Skipping already applied migration version: $versionDir\n";
            continue;
        }

        $fullPath = realpath($basePath . '/' . $versionDir);
        if (is_dir($fullPath)) {
            echo "Processing directory: $fullPath\n";
            $files = scandir($fullPath);
            $files = array_filter($files, function ($file) use ($fullPath) {
                return isSQLorPHPFile($file) && file_exists($fullPath . '/' . $file);
            });

            if (empty($files)) {
                echo "No migrations found in $fullPath\n";
                continue;
            }

            try {
                $pdo->beginTransaction();
                foreach ($files as $file) {
                    $sql = file_get_contents($fullPath . '/' . $file);
                    echo "Executing SQL from: $file\n";
                    $pdo->exec($sql);
                    echo "Applied migration: $file\n";
                    $pdo->exec("INSERT INTO migrations_version (version, script) VALUES ('$versionDir', '$file')");
                }
                $pdo->commit();
                echo "Registered migration: $file in database.\n";
            } catch (PDOException $e) {
                echo "Migration error: " . $e->getMessage() . "\n";
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
            }
        }
    }
}

function isSQLorPHPFile($fileName) {
    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
    return $ext === 'sql' || $ext === 'php';
}

// Usar una ruta absoluta
$basePath = '/appdata/www/migrations/client';
echo "Base Path for client migrations: $basePath\n";

foreach ($clients as $client) {
    $dbName = $client['databaseName'];
    $host = $client['host'];
    $username = $client['username'];
    $password = $client['password'];
    echo "Migrando base de datos del cliente: $dbName en el host: $host\n";
    $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $password);
    applyMigrations($pdo, $basePath);
}
