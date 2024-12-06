<?php

namespace App\Service;

use App\Entity\Main\Client;
use Doctrine\DBAL\DriverManager;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class MigrationService
{
    private LoggerInterface $logger;
    private string $migrationsPath;

    public function __construct(LoggerInterface $logger, string $migrationsPath)
    {
        $this->logger = $logger;
        $this->migrationsPath = $migrationsPath;
    }

    public function applyMigrations(Client $client): void
    {
        // Configurar la conexión a la base de datos del cliente
        $connectionParams = [
            'dbname' => $client->getDatabaseName(),
            'user' => $client->getDatabaseUserName(),
            'password' => $client->getDatabasePassword(),
            'host' => $client->getHost(),
            'port' => $client->getPortBbdd(),
            'driver' => 'pdo_mysql',
        ];

        $connection = DriverManager::getConnection($connectionParams);

        // Configurar Doctrine Migrations
        $config = new PhpFile($this->migrationsPath.'/migrations.php'); // Ruta a tu configuración de migraciones
        $dependencyFactory = DependencyFactory::fromConnection($config, $connection);

        // Ejecutar las migraciones
        $migrate = new MigrateCommand($dependencyFactory);

        $input = new ArrayInput([
            '--no-interaction' => true,
        ]);

        $output = new BufferedOutput();

        try {
            $migrate->run($input, $output);
            $this->logger->info("Migraciones aplicadas exitosamente para el cliente {$client->getClientName()}");
        } catch (\Exception $e) {
            $this->logger->error("Error al aplicar migraciones para el cliente {$client->getClientName()}: ".$e->getMessage());
            throw $e;
        }
    }
}
