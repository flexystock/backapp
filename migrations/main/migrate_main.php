<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Detectar si estamos en CI (GitHub Actions) o en Docker
$host = getenv('CI') ? '127.0.0.1' : 'docker-symfony-dbMain';
$user = getenv('DB_USER') ?: 'user';
$pass = getenv('DB_PASSWORD') ?: 'password';
$dbName = 'docker_symfony_databaseMain';

echo "Connecting to: $host\n";
echo "Database: $dbName\n";

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbName};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    echo "✅ Connected successfully\n";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Crear la tabla migrations_version si no existe
function createMigrationsTable($pdo)
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS migrations_version (
            id INT AUTO_INCREMENT PRIMARY KEY,
            version VARCHAR(255) NOT NULL,
            script VARCHAR(255) NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_version_script(version, script)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "✅ Table 'migrations_version' checked/created.\n";
}

function applyMigrations($pdo, $basePath)
{
    if (!is_dir($basePath)) {
        echo "❌ Invalid migrations path: $basePath\n";
        return;
    }

    echo "📁 Base Path for migrations: $basePath\n";
    createMigrationsTable($pdo);

    // Obtener versiones ya aplicadas
    $applied = [];
    $q = $pdo->query("SELECT version, script FROM migrations_version");
    foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $applied[$row['version']][$row['script']] = true;
    }

    // Ordenar directorios naturalmente
    $dirs = array_filter(scandir($basePath), function($d) use ($basePath) {
        return $d !== '.' && $d !== '..' && is_dir($basePath . '/' . $d);
    });
    natsort($dirs);

    foreach ($dirs as $versionDir) {
        $versionPath = $basePath . '/' . $versionDir;
        echo "\n📂 Processing directory: $versionDir\n";

        // Obtener archivos SQL
        $files = array_filter(scandir($versionPath), function($f) use ($versionPath) {
            return pathinfo($f, PATHINFO_EXTENSION) === 'sql' && is_file($versionPath . '/' . $f);
        });
        natsort($files);

        if (empty($files)) {
            echo "⚠️  No SQL files found in $versionPath\n";
            continue;
        }

        foreach ($files as $file) {
            if (isset($applied[$versionDir][$file])) {
                echo "⏭️  Skipping already applied: $file\n";
                continue;
            }

            $fullFile = $versionPath . '/' . $file;
            echo "⚙️  Executing: $file\n";

            try {
                $sql = file_get_contents($fullFile);
                if ($sql === false) {
                    throw new RuntimeException("Could not read $fullFile");
                }

                $pdo->exec($sql);

                $ins = $pdo->prepare("INSERT INTO migrations_version (version, script) VALUES (?, ?)");
                $ins->execute([$versionDir, $file]);

                echo "✅ Applied migration: $file\n";
            } catch (Throwable $e) {
                echo "❌ Migration error in $file: " . $e->getMessage() . "\n";
                exit(1); // Fallar si hay error
            }
        }
    }

    echo "\n✅ All main migrations applied successfully\n";
}

function isSQLFile($fileName)
{
    return pathinfo($fileName, PATHINFO_EXTENSION) === 'sql';
}

// Ruta base de migraciones (compatible con CI y Docker)
$basePath = __DIR__;
if (!is_dir($basePath)) {
    $basePath = '/appdata/www/migrations/main';
}

echo "🚀 Starting Main DB migrations\n";
echo "=" . str_repeat("=", 50) . "\n";
applyMigrations($pdo, $basePath);
echo str_repeat("=", 50) . "\n";