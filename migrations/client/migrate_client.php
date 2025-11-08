<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$clientIdentifier = $argv[1] ?? null;

// Detectar entorno
$isCI = getenv('CI') !== false;
$mainHost = $isCI ? '127.0.0.1' : 'docker-symfony-dbMain';
$mainUser = $isCI ? 'root' : 'user';
$mainPass = $isCI ? 'root' : 'password';

echo "🔧 Environment: " . ($isCI ? "CI (GitHub Actions)" : "Docker") . "\n";
echo "🔌 Main DB Host: $mainHost\n\n";

// Conexión a la BD principal
try {
    $mainPdo = new PDO(
        "mysql:host={$mainHost};dbname=docker_symfony_databaseMain;charset=utf8mb4",
        $mainUser,
        $mainPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    echo "✅ Connected to main database\n\n";
} catch (PDOException $e) {
    echo "❌ Error connecting to main database: " . $e->getMessage() . "\n";
    exit(1);
}

// Obtener clientes
if ($clientIdentifier !== null) {
    $sql = "SELECT uuid_client, database_name, host, port_bbdd, database_user_name, database_password 
            FROM client 
            WHERE uuid_client = :id OR database_name = :id2";
    $stmt = $mainPdo->prepare($sql);
    $stmt->execute(['id' => $clientIdentifier, 'id2' => $clientIdentifier]);
    $clients = $stmt->fetchAll();
} else {
    $sql = "SELECT uuid_client, database_name, host, port_bbdd, database_user_name, database_password 
            FROM client";
    $stmt = $mainPdo->query($sql);
    $clients = $stmt->fetchAll();
}

if (!$clients) {
    echo "⚠️  No clients found for migrations.\n";
    exit(0);
}

function isSqlFile(string $file): bool {
    return strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'sql';
}

function applyMigrations(PDO $pdo, string $basePath): void {
    if (!is_dir($basePath)) {
        echo "❌ Invalid migrations path: $basePath\n";
        return;
    }

    // Tabla de tracking
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS migrations_version (
            id INT AUTO_INCREMENT PRIMARY KEY,
            version VARCHAR(50) NOT NULL,
            script  VARCHAR(255) NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_version_script(version, script)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Obtener versiones aplicadas
    $applied = [];
    $q = $pdo->query("SELECT version, script FROM migrations_version");
    foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $applied[$row['version']][$row['script']] = true;
    }

    // Ordenar directorios
    $dirs = array_values(array_filter(scandir($basePath), function($d) use ($basePath){
        return $d !== '.' && $d !== '..' && is_dir($basePath.'/'.$d);
    }));
    natsort($dirs);

    foreach ($dirs as $versionDir) {
        $versionPath = $basePath.'/'.$versionDir;
        echo "📂 Processing directory: $versionDir\n";

        // Archivos SQL del directorio
        $files = array_values(array_filter(scandir($versionPath), function($f) use ($versionPath){
            return isSqlFile($f) && is_file($versionPath.'/'.$f);
        }));
        natsort($files);

        if (empty($files)) {
            echo "⚠️  No migrations found in $versionPath\n";
            continue;
        }

        foreach ($files as $file) {
            if (isset($applied[$versionDir][$file])) {
                echo "⏭️  Skipping already applied: $file\n";
                continue;
            }

            $fullFile = $versionPath.'/'.$file;
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
                echo "❌ Migration error in $file: ".$e->getMessage()."\n";
                exit(1);
            }
        }
    }
}

// Ruta de migraciones (compatible con CI y Docker)
$basePath = __DIR__;
if (!is_dir($basePath)) {
    $basePath = '/appdata/www/migrations/client';
}

echo "🚀 Starting Client DB migrations\n";
echo str_repeat("=", 60) . "\n";

foreach ($clients as $c) {
    $dbName = $c['database_name'];
    $host = $c['host'];
    $user = $c['database_user_name'];
    $pass = $c['database_password'];

    // En CI usar 3306, en Docker también 3306 (red interna)
    $port = 3306;

    echo "\n" . str_repeat("=", 60) . "\n";
    echo "👤 Migrating client: $dbName\n";
    echo "🔌 Host: $host:$port\n";
    echo "👤 User: $user\n";
    echo str_repeat("=", 60) . "\n";

    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";

        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        echo "✅ Connection successful\n";
        applyMigrations($pdo, $basePath);
        echo "✅ Client migrations completed\n";

    } catch (PDOException $e) {
        echo "❌ Error connecting to database ($dbName @ $host:$port): ".$e->getMessage()."\n";
        exit(1);
    }
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ All client migrations completed successfully\n";
echo str_repeat("=", 60) . "\n";