<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$clientIdentifier = $argv[1] ?? null;

// Conexión a la BD principal
$mainPdo = new PDO(
    'mysql:host=docker-symfony-dbMain;dbname=docker_symfony_databaseMain;charset=utf8mb4',
    'user',
    'password',
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
);

/// Obtener clientes (por uuid o por database_name si se pasa argumento)
if ($clientIdentifier !== null) {
    $sql = "SELECT uuid_client, database_name, host, port_bbdd, database_user_name, database_password FROM client WHERE uuid_client = :id OR database_name = :id2";
    $stmt = $mainPdo->prepare($sql);
    $stmt->execute(['id' => $clientIdentifier, 'id2' => $clientIdentifier]);
    $clients = $stmt->fetchAll();
} else {
    $sql = "SELECT uuid_client, database_name, host, port_bbdd, database_user_name, database_password FROM client";
    $stmt = $mainPdo->query($sql);
    $clients = $stmt->fetchAll();
}

if (!$clients) {
    echo "No se encontraron clientes para aplicar las migraciones.\n";
    exit(0);
}

function isSqlOrPhpFile(string $file): bool {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    return in_array($ext, ['sql','php'], true);
}

/**
 * Aplica migraciones en orden por carpeta (001, 002, …) y fichero.
 * No usa transacciones globales porque DDL hace commits implícitos.
 */
function applyMigrations(PDO $pdo, string $basePath): void {
    if (!is_dir($basePath)) {
        echo "Invalid migrations path: $basePath\n";
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

    // Obtener últimas versiones ya aplicadas por versión->scripts
    $applied = [];
    $q = $pdo->query("SELECT version, script FROM migrations_version");
    foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $applied[$row['version']][$row['script']] = true;
    }

    // Ordenar directorios (naturally: 001, 002, …)
    $dirs = array_values(array_filter(scandir($basePath), function($d){
        return $d !== '.' && $d !== '..' && is_dir($GLOBALS['basePath'].'/'.$d);
    }));
    natsort($dirs);

    foreach ($dirs as $versionDir) {
        $versionPath = $basePath.'/'.$versionDir;
        echo "Processing directory: $versionPath\n";

        // Archivos del directorio ordenados
        $files = array_values(array_filter(scandir($versionPath), function($f) use ($versionPath){
            return isSqlOrPhpFile($f) && is_file($versionPath.'/'.$f);
        }));
        natsort($files);

        if (empty($files)) {
            echo "No migrations found in $versionPath\n";
            continue;
        }

        foreach ($files as $file) {
            if (isset($applied[$versionDir][$file])) {
                echo "Skipping already applied: $versionDir/$file\n";
                continue;
            }

            $fullFile = $versionPath.'/'.$file;
            echo "Executing: $file\n";

            try {
                if (str_ends_with(strtolower($file), '.sql')) {
                    $sql = file_get_contents($fullFile);
                    if ($sql === false) {
                        throw new RuntimeException("No se pudo leer $fullFile");
                    }
                    $pdo->exec($sql);
                } else {
                    // Si tuvieras migraciones PHP, podrías incluirlas aquí.
                    // require $fullFile;
                    throw new RuntimeException("Soporte .php no implementado para migraciones");
                }

                $ins = $pdo->prepare("INSERT INTO migrations_version (version, script) VALUES (?, ?)");
                $ins->execute([$versionDir, $file]);
                echo "Applied migration: $versionDir/$file\n";
            } catch (Throwable $e) {
                // NO rollback aquí (no hay transacción); solo reporta y continúa o haz exit si quieres parar.
                echo "Migration error en $versionDir/$file: ".$e->getMessage()."\n";
                // Si prefieres parar en el primer error, descomenta:
                // exit(1);
            }
        }
    }
}

// Ruta absoluta de migraciones
$basePath = '/appdata/www/migrations/client';
echo "Base Path for client migrations: $basePath\n";

foreach ($clients as $c) {
    $dbName   = $c['database_name'];
    $host     = $c['host'];
    $pubPort  = (int)$c['port_bbdd'];         // puerto publicado en el host
    $user     = $c['database_user_name'];
    $pass     = $c['database_password'];

    // Elegir puerto correcto:
    // - Si host es 'localhost' o '127.0.0.1' -> usar puerto publicado (host -> contenedor por NAT)
    // - Si host parece nombre de contenedor (te conectas por la red docker) -> usar 3306
    $hostLower = strtolower($host);
    if (in_array($hostLower, ['localhost','127.0.0.1'], true)) {
        $port = $pubPort ?: 3306;     // para pruebas desde host
    } else {
        $port = 3306;                 // desde docker-symfony-be por red interna
    }

    echo "Migrando base de datos del cliente: $dbName en el host: $host:$port\n";

    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
        applyMigrations($pdo, $basePath);
    } catch (PDOException $e) {
        echo "Error al conectar con la base de datos ($dbName @ $host:$port): ".$e->getMessage()."\n";
    }
}
