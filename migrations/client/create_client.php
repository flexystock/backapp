<?php
function generateUUID() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

function createClient($clientName, $databaseName, $host, $username, $password) {
    $uuid = generateUUID();
    $pdo = new PDO('mysql:host=docker-symfony-dbMain;dbname=docker_symfony_databaseMain', 'user', 'password');

    $stmt = $pdo->prepare("INSERT INTO client (uuid, clientName, databaseName, host, username, password) VALUES (:uuid, :clientName, :databaseName, :host, :username, :password)");
    $stmt->bindParam(':uuid', $uuid);
    $stmt->bindParam(':clientName', $clientName);
    $stmt->bindParam(':databaseName', $databaseName);
    $stmt->bindParam(':host', $host);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);

    if ($stmt->execute()) {
        echo "Client '$clientName' created successfully with UUID: $uuid\n";
    } else {
        echo "Failed to create client '$clientName'\n";
    }
}

// Leer parámetros desde la línea de comandos
if ($argc !== 6) {
    echo "Usage: php create_client.php <clientName> <databaseName> <host> <username> <password>\n";
    exit(1);
}

$clientName = $argv[1];
$databaseName = $argv[2];
$host = $argv[3];
$username = $argv[4];
$password = $argv[5];

createClient($clientName, $databaseName, $host, $username, $password);
