<?php
$pdo = new PDO('mysql:host=docker-symfony-dbMain;dbname=docker_symfony_databaseMain', 'user', 'password');

function applyMigrations($pdo, $basePath) {
    if ($basePath === false || !is_dir($basePath)) {
        echo "Invalid migrations path: $basePath\n";
        return;
    }

    // Verificar que la ruta base exista
    echo "Base Path for migrations: $basePath\n";

    // Obtener la última versión ejecutada
    $lastVersion = $pdo->query("SELECT MAX(version) FROM migrations_version")->fetchColumn();
    if ($lastVersion === false) {
        $lastVersion = '000';
    }

    echo "Last applied migration version: $lastVersion\n";

    foreach (scandir($basePath) as $versionDir) {
        if ($versionDir === '.' || $versionDir === '..') continue;

        echo "Checking directory: $versionDir\n";

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
                echo "Found file: $file\n"; // Depuración
                return isSQLorPHPFile($file) && file_exists($fullPath . '/' . $file);
            });

            if (empty($files)) {
                echo "No valid SQL or PHP files found in $fullPath\n";
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
        } else {
            echo "Directory $fullPath is not valid.\n";
        }
    }
}

function isSQLorPHPFile($fileName) {
    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
    return $ext === 'sql' || $ext === 'php';
}

// Usar una ruta absoluta
$basePath = '/appdata/www/migrations/main';
applyMigrations($pdo, $basePath);
