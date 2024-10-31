<?php
// Obtener el argumento opcional del cliente
$clientIdentifier = $argv[1] ?? null;

// Conectar a la base de datos principal
$mainPdo = new PDO('mysql:host=docker-symfony-dbMain;dbname=docker_symfony_databaseMain', 'user', 'password');

// Construir la consulta para obtener los clientes
$sql = "SELECT database_name, host, port_bbdd, database_user_name, database_password FROM client";

if ($clientIdentifier !== null) {
    $sql .= " WHERE uuid_Client = :identifier OR database_name = :identifier";
}

$stmt = $mainPdo->prepare($sql);

if ($clientIdentifier !== null) {
    $stmt->bindValue(':identifier', $clientIdentifier);
}

$stmt->execute();

$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar si se encontró al menos un cliente
if (empty($clients)) {
    echo "No se encontraron clientes para aplicar las migraciones.\n";
    exit;
}

// Obtener la IP del host desde el contenedor
$hostIp = '127.0.0.1'; // O la IP que corresponda en tu entorno

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
                file_put_contents('/var/log/migrations.log', "Error en la migración: " . $e->getMessage() . "\n", FILE_APPEND);
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
    $dbName = $client['database_name'];
    $host = $client['host'];
    $port = $client['port_bbdd'];
    $username = $client['database_user_name'];
    $password = $client['database_password'];

    // Si el host es 'localhost', lo reemplazamos por la IP del host
    if ($host === 'localhost') {
        $host = '127.0.0.1';

    }

    echo "Migrando base de datos del cliente: $dbName en el host: $host\n";

    //$dsn = "mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4";

    try {
        //$pdo = new PDO($dsn, $username, $password);
        $pdo = new PDO("mysql:host=$host;port=3306;dbname=$dbName;charset=utf8mb4", "$username", "$password");
        applyMigrations($pdo, $basePath);
    } catch (PDOException $e) {
        echo "Error al conectar con la base de datos: " . $e->getMessage() . "\n";
    }
}
