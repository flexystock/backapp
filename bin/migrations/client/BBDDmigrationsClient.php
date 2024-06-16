<?php

use Symfony\Component\Dotenv\Dotenv;
$basePath = realpath(__DIR__ . '/../../../vendor/autoload.php');
require $basePath;  // Asegúrate de tener autoload para cargar las clases necesarias

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../../../.env');  // Ajusta la ruta según sea necesario para tu estructura de directorio


////////////////////////////////////////////////////////////////////77

// Conectar a la base de datos principal
$pdoMain = new PDO('mysql:host=docker-symfony-db;dbname=docker_symfony_database', 'user', 'password');

function applyMigrationsForClient($pdoClient, $clientIdentifier) {
    echo "Aplicando migraciones para el cliente: $clientIdentifier\n";
    $basePath = realpath(__DIR__ . '/../../../migrations/client');
    $pdoClient->beginTransaction(); // Inicia la transacción
    try {
        foreach (scandir($basePath) as $versionDir) {
            if ($versionDir === '.' || $versionDir === '..') continue;
            $fullPath = realpath($basePath . '/' . $versionDir);
            if (is_dir($fullPath)) {
                echo "Procesando directorio: $fullPath\n";
                foreach (scandir($fullPath) as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
                        $sql = file_get_contents($fullPath . '/' . $file);
                        $pdoClient->exec($sql);
                        echo "Ejecutada migración: $file\n";
                        // Actualiza o inserta la nueva versión de migración
                        $updateMigrationSQL = "INSERT INTO migrations (migration, version, batch) 
                                                VALUES ('$file', '$versionDir', 1) 
                                                ON DUPLICATE KEY UPDATE migration='$file', version='$versionDir', batch=batch+1";
                        $pdoClient->exec($updateMigrationSQL);
                    }
                }
            }
        }
        $pdoClient->commit(); // Confirma la transacción
    } catch (PDOException $e) {
        $pdoClient->rollBack(); // Revierte la transacción si hay error
        echo "Error en migración: " . $e->getMessage() . "\n";
    }
}


// Obtener clientes de la base de datos principal
$stmt = $pdoMain->query("SELECT scheme FROM clients");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($clients as $client) {
    $clientIdentifier = $client['scheme'];
    echo "Identificador del Cliente: $clientIdentifier\n";
    $databaseUrl = $_ENV[strtoupper($clientIdentifier) . '_DATABASE_URL'];
    echo "DataBase URL: \n";
    var_dump($databaseUrl);
    // Elimina la parte de query string si existe
    $urlComponents = parse_url(explode('?', $databaseUrl)[0]);
    echo "componentes URL:\n";
    var_dump($urlComponents);
    if (!$urlComponents || !isset($urlComponents['host'], $urlComponents['user'])) {
        echo "Error: URL de base de datos mal formado para $clientIdentifier\n";
        continue;
    }

    $dbName = ltrim($urlComponents['path'], '/'); // Asegura remover la barra inicial
    $dbHost = $urlComponents['host'];
    $dbUser = $urlComponents['user'];
    $dbPass = $urlComponents['pass'] ?? ''; // Usa null coalescing operator para el password
    $dbPort = $urlComponents['port'] ?? 3306; // Puerto por defecto para MySQL si no se especifica

    //$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4;port=$dbPort";
    $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4;port=3306";
    try {
        $pdoClient = new PDO($dsn, $dbUser, $dbPass);
        //$pdoClient = new PDO("mysql:host=docker-symfony-dbClient;port=3306;", 'user', 'password');
        echo "Aplicando migraciones para el cliente: $clientIdentifier\n";
        applyMigrationsForClient($pdoClient, $clientIdentifier);
    } catch (PDOException $e) {
        echo "Error al conectar con la base de datos para $clientIdentifier: " . $e->getMessage() . "\n";
    }
}
