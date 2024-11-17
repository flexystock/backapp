<?php

namespace App\MessageHandler;

use App\Client\Application\OutputPorts\ClientRepositoryInterface;
use App\Message\CreateDockerContainerMessage;
use App\Service\DockerService;
use PDO;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Process\Exception\ProcessFailedException;

//use App\Repository\ClientRepositoryInterface;
//use PDOException as PDOExceptionAlias;


#[AsMessageHandler(handles: CreateDockerContainerMessage::class)]
class CreateDockerContainerMessageHandler
{
    private ClientRepositoryInterface $clientRepository;
    private DockerService $dockerService;
    private LoggerInterface $logger;

    public function __construct(ClientRepositoryInterface $clientRepository,
                                DockerService $dockerService,
                                LoggerInterface $logger)
    {
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
            //die("llegamos al messageHandler");
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

            $client= $this->dockerService->createClientDatabase($client);
            $this->clientRepository->save($client);

            // Verificar que la base de datos esté lista antes de aplicar migraciones
            $host = $client->getHost();
            $port = $client->getPortBbdd();
            $username = $client->getDatabaseUserName();
            $password = $client->getDatabasePassword();
            $databaseName = $client->getDatabaseName();

            if ($this->waitForDatabaseToBeReady($host, $port, $username, $password, $databaseName)) {
                // Ejecutar el script migrate_client.php para el cliente específico
                $this->logger->debug("CONSEGUIMOS CONECTAR");
                $scriptPath = '/appdata/www/migrations/client/migrate_client.php';
                $clientIdentifier = $client->getUuidClient();
                $this->logger->debug("IDENTIFICADOR DEL CLIENTE: ".$clientIdentifier);
                $command = "php {$scriptPath} {$clientIdentifier}";
                $this->logger->info("Ejecutando comando: {$command}");

                exec($command, $output, $returnVar);

                $this->logger->debug("Valor de retorno: {$returnVar}");
                $this->logger->debug("Salida del comando: " . implode("\n", $output));

                if ($returnVar !== 0) {
                    $this->logger->error('Error al ejecutar migrate_client.php: ' . implode("\n", $output));
                    throw new \Exception('Error al ejecutar migrate_client.php');
                } else {
                    $this->logger->info("Migraciones aplicadas exitosamente para el cliente {$client->getClientName()}");
                }
            } else {
                throw new \Exception("No se pudo conectar a la base de datos después de múltiples intentos.");
            }
        } catch (ProcessFailedException $e) {
            $this->logger->error('ERROR al crear el contenedor Docker: ' . $e->getMessage());
            throw $e; // Re-lanzar la excepción para que Messenger gestione el reintento
        } catch (\Exception $e) {
            $this->logger->error('Error inesperado: ' . $e->getMessage());
            throw $e;
        }
    }

    private function waitForDatabaseToBeReady($host, $port, $username, $password, $databaseName): bool
    {
        $retries = 0;
        while ($retries < 10) {
            try {
                $this->logger->debug('HOST: '.$host);
                $this->logger->debug('PORT: '.$port );
                $this->logger->debug('DBNAME: '. $databaseName);
                $this->logger->debug('USERNAME: '. $username);
                $this->logger->debug('PASSWORD: '. $password);

                $pdo = new PDO("mysql:host=$host;port=3306;dbname=$databaseName;charset=utf8mb4", "$username", "$password");
                return true;
            } catch (\PDOException $e) {
                $this->logger->warning("Intento {$retries}: No se puede conectar a la base de datos: {$e->getMessage()}");
                $retries++;
                sleep(5); // Esperar antes de intentar de nuevo
            }
        }

        $this->logger->error("No se pudo conectar a la base de datos después de 10 intentos.");
        return false;
    }
}
