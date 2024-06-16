<?php
$pdo = new PDO('mysql:host=docker-symfony-db;dbname=docker_symfony_database', 'user', 'password');

function applyMigrations($pdo, $basePath) {
    foreach (scandir($basePath) as $versionDir) {
        if ($versionDir === '.' || $versionDir === '..') continue; // Ignorar directorios actuales y padre

        $fullPath = realpath($basePath . '/' . $versionDir);
        if (is_dir($fullPath)) {
            echo "Procesando directorio: $fullPath\n";
            $files = scandir($fullPath);
            $files = array_filter($files, function ($file) use ($fullPath) {
                return isSQLorPHPFile($file) && file_exists($fullPath . '/' . $file);
            });

            if (empty($files)) {
                echo "No se encontraron migraciones en $fullPath\n";
                continue;
            }

            try {
                $pdo->beginTransaction();
                foreach ($files as $file) {
                    $sql = file_get_contents($fullPath . '/' . $file);
                    echo "Ejecutando SQL de: $file\n";
                    $pdo->exec($sql);
                    echo "Aplicada migración: $file\n";
                    $pdo->exec("INSERT INTO migrations (migration, version, batch) VALUES ('$file', '$versionDir', 1)");
                }
                $pdo->commit();
                echo "Registrada migración: $file en base de datos.\n";
            } catch (PDOException $e) {
                echo "Error en migración: " . $e->getMessage() . "\n";
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

$basePath = realpath(__DIR__ . '/../../../migrations/main');
echo "Base Path for migrations: $basePath\n";
applyMigrations($pdo, $basePath);