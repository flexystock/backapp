<?php
namespace App\Tests;

use PHPUnit\Framework\TestCase;
use PDO;

class DatabaseTest extends TestCase
{
    private PDO $mainPdo;
    private PDO $clientPdo;

    protected function setUp(): void
    {
        $isCI = getenv('CI') !== false;

        if ($isCI) {
            // En GitHub Actions
            $mainHost = '127.0.0.1';
            $mainPort = '3306';
            $mainUser = 'root';
            $mainPass = 'root';
            $mainDbName = 'docker_symfony_databaseMain';

            $clientHost = '127.0.0.1';
            $clientPort = '3306';
            $clientUser = 'root';
            $clientPass = 'root';
            $clientDbName = 'test_client_db';
        } else {
            // En local: Docker con puertos mapeados
            $mainHost = '127.0.0.1';
            $mainPort = '40099';
            $mainUser = 'user';
            $mainPass = 'password';
            $mainDbName = 'docker_symfony_databaseMain';

            // Conectar a main DB primero para obtener credenciales del cliente
            $tempMainPdo = new PDO(
                "mysql:host={$mainHost};port={$mainPort};dbname={$mainDbName}",
                $mainUser,
                $mainPass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Obtener credenciales del cliente desde la BD main
            // @phpstan-ignore-next-line
            /** @noinspection SqlResolve */
            $stmt = $tempMainPdo->prepare(
                "SELECT database_name, host, port_bbdd, database_user_name, database_password 
                 FROM `client` 
                 WHERE database_name LIKE 'client_db_pruebas%' 
                 LIMIT 1"
            );
            $stmt->execute();
            $clientInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$clientInfo) {
                throw new \RuntimeException("No test client found in database");
            }

            $clientHost = '127.0.0.1';
            $clientPort = $clientInfo['port_bbdd'];
            $clientUser = $clientInfo['database_user_name'];
            $clientPass = $clientInfo['database_password'];
            $clientDbName = $clientInfo['database_name'];
        }

        echo "🔧 Test Environment: " . ($isCI ? "CI" : "Local Docker") . "\n";
        echo "🔌 Main DB: {$mainHost}:{$mainPort} (user: {$mainUser})\n";
        echo "🔌 Client DB: {$clientHost}:{$clientPort} (user: {$clientUser})\n\n";

        try {
            $this->mainPdo = new PDO(
                "mysql:host={$mainHost};port={$mainPort};dbname={$mainDbName}",
                $mainUser,
                $mainPass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            echo "✅ Connected to main database\n";
        } catch (\PDOException $e) {
            echo "❌ Failed to connect to main DB: " . $e->getMessage() . "\n";
            throw $e;
        }

        try {
            $this->clientPdo = new PDO(
                "mysql:host={$clientHost};port={$clientPort};dbname={$clientDbName}",
                $clientUser,
                $clientPass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            echo "✅ Connected to client database: {$clientDbName}\n\n";
        } catch (\PDOException $e) {
            echo "❌ Failed to connect to client DB: " . $e->getMessage() . "\n";
            echo "💡 Database: {$clientDbName}\n";
            echo "💡 User: {$clientUser}\n";
            throw $e;
        }
    }

    public function testMainDatabaseHasMigrationsTable(): void
    {
        $stmt = $this->mainPdo->query("SHOW TABLES LIKE 'migrations_version'");
        $result = $stmt->fetch();

        $this->assertNotFalse($result, 'Table migrations_version should exist in main database');
    }

    public function testClientDatabaseHasMigrationsTable(): void
    {
        $stmt = $this->clientPdo->query("SHOW TABLES LIKE 'migrations_version'");
        $result = $stmt->fetch();

        $this->assertNotFalse($result, 'Table migrations_version should exist in client database');
    }

    public function testClientExistsInMainDatabase(): void
    {
        $isCI = getenv('CI') !== false;

        if ($isCI) {
            $clientDbName = 'test_client_db';
        } else {
            // Obtener el nombre de la BD del cliente actual
            $stmt = $this->clientPdo->query("SELECT DATABASE() as db_name");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $clientDbName = $result['db_name'];
        }
        // @phpstan-ignore-next-line
        /** @noinspection SqlResolve */
        $stmt = $this->mainPdo->prepare(
            "SELECT * FROM `client` WHERE database_name = :dbname"
        );
        $stmt->execute(['dbname' => $clientDbName]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotFalse($result, "Client {$clientDbName} should exist in main database");
        $this->assertEquals($clientDbName, $result['database_name']);
    }

    public function testCanConnectToClientDatabase(): void
    {
        $this->assertInstanceOf(PDO::class, $this->clientPdo);

        $stmt = $this->clientPdo->query("SELECT DATABASE() as db_name");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotEmpty($result['db_name']);
        echo "📊 Successfully connected to: " . $result['db_name'] . "\n";
    }

    protected function tearDown(): void
    {
        unset($this->mainPdo);
        unset($this->clientPdo);
    }
}