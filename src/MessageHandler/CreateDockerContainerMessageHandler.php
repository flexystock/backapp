<?php

namespace App\MessageHandler;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Message\CreateDockerContainerMessage;
use App\Service\DockerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Process\Exception\ProcessFailedException;

// use App\Repository\ClientRepositoryInterface;
// use PDOException as PDOExceptionAlias;

#[AsMessageHandler(handles: CreateDockerContainerMessage::class)]
class CreateDockerContainerMessageHandler
{
    private ClientRepositoryInterface $clientRepository;
    private DockerService $dockerService;
    private LoggerInterface $logger;

    /**
     * Create the handler with required services.
     *
     * @param ClientRepositoryInterface $clientRepository Repository to persist client changes
     * @param DockerService             $dockerService    Service used to create the docker container
     * @param LoggerInterface           $logger           Logger instance for debugging
     */
    public function __construct(
        ClientRepositoryInterface $clientRepository,
        DockerService $dockerService,
        LoggerInterface $logger
    ) {
        $this->clientRepository = $clientRepository;
        $this->dockerService = $dockerService;
        $this->logger = $logger;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(CreateDockerContainerMessage $message): void
    {
        try {
            $this->logger->info('Iniciando procesamiento de CreateDockerContainerMessage');
            // die("llegamos al messageHandler");
            $clientUuid = $message->getClientId();
            $this->logger->info("Obteniendo cliente con UUID: {$clientUuid}");
            $client = $this->clientRepository->findOneBy(['uuid_client' => $clientUuid]);

            if (!$client) {
                $this->logger->warning("CLIENT WHITH UUID {$clientUuid} not found. Discarding message.");

                return;
            }
            $this->logger->info('Propiedades del cliente:', [
                'uuid_client' => $client->getUuidClient(),
                'name' => $client->getName(),
            ]);

            $client = $this->dockerService->createClientDatabase($client);
            $this->clientRepository->save($client);

            // Verificar que la base de datos esté lista antes de aplicar migraciones
            $host = $client->getHost();
            $port = $client->getPortBbdd();
            $username = $client->getDatabaseUserName();
            $password = $client->getDatabasePassword();
            $databaseName = $client->getDatabaseName();

            if ($this->waitForDatabaseToBeReady($host, $port, $username, $password, $databaseName)) {
                // Ejecutar el script migrate_client.php para el cliente específico
                $this->logger->debug('CONSEGUIMOS CONECTAR');
                $scriptPath = '/appdata/www/migrations/client/migrate_client.php';
                $clientIdentifier = $client->getUuidClient();
                $this->logger->debug('IDENTIFICADOR DEL CLIENTE: '.$clientIdentifier);
                $command = "php {$scriptPath} {$clientIdentifier}";
                $this->logger->info("Ejecutando comando: {$command}");

                exec($command, $output, $returnVar);

                $this->logger->debug("Valor de retorno: {$returnVar}");
                $this->logger->debug('Salida del comando: '.implode("\n", $output));

                if (0 !== $returnVar) {
                    $this->logger->error('Error al ejecutar migrate_client.php: '.implode("\n", $output));
                    throw new \Exception('Error al ejecutar migrate_client.php');
                } else {
                    $this->logger->info("Migraciones aplicadas exitosamente para el cliente {$client->getClientName()}");
                }
            } else {
                throw new \Exception('No se pudo conectar a la base de datos después de múltiples intentos.');
            }
        } catch (ProcessFailedException $e) {
            $this->logger->error('ERROR al crear el contenedor Docker: '.$e->getMessage());
            throw $e; // Re-lanzar la excepción para que Messenger gestione el reintento
        } catch (\Exception $e) {
            $this->logger->error('Error inesperado: '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * Try to connect to the database several times until it is available.
     *
     * @param string $host         database host
     * @param int    $port         port where MySQL is listening
     * @param string $username     username to authenticate
     * @param string $password     password for the database user
     * @param string $databaseName name of the database to connect to
     *
     * @return bool true if connection succeeds before exhausting retries
     */
    private function waitForDatabaseToBeReady($host, $port, $username, $password, $databaseName): bool
    {
        $retries = 0;
        // IMPORTANTE: Desde docker-symfony-be, conectarse al puerto interno 3306
        // NO al puerto publicado que es solo para acceso desde fuera
        $internalPort = 3306;

        $this->logger->info('Intentando conectar a la base de datos del cliente', [
            'host' => $host,
            'port_interno' => $internalPort,
            'port_publicado' => $port,
            'database' => $databaseName,
            'username' => $username,
        ]);
        while ($retries < 10) {
            try {
                $pdo = new \PDO(
                    "mysql:host={$host};port={$internalPort};dbname={$databaseName};charset=utf8mb4",
                    $username,
                    $password,
                    [\PDO::ATTR_TIMEOUT => 3]
                );

                $this->logger->info('Conexión exitosa a la base de datos del cliente');
                return true;
            } catch (\PDOException $e) {
                $this->logger->warning("Intento {$retries}: No se puede conectar: {$e->getMessage()}");
                ++$retries;
                sleep(5);
            }
        }

        $this->logger->error('No se pudo conectar a la base de datos después de 10 intentos.');
        return false;
    }
}
