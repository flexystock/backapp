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
            $this->logger->info('=== Iniciando creación de contenedor Docker para cliente ===');

            $clientUuid = $message->getClientId();
            $this->logger->info("Buscando cliente con UUID: {$clientUuid}");

            $client = $this->clientRepository->findOneBy(['uuid_client' => $clientUuid]);

            if (!$client) {
                $this->logger->warning("Cliente con UUID {$clientUuid} no encontrado. Descartando mensaje.");
                return;
            }

            $this->logger->info('Cliente encontrado:', [
                'uuid' => $client->getUuidClient(),
                'name' => $client->getName(),
            ]);

            // 1. Crear contenedor Docker y configurar la base de datos
            $this->logger->info('Paso 1: Creando contenedor Docker...');
            $client = $this->dockerService->createClientDatabase($client);

            // 2. ✅ IMPORTANTE: Guardar ANTES de ejecutar migraciones
            $this->logger->info('Paso 2: Guardando configuración del cliente en BBDD principal...');
            $this->clientRepository->save($client);

            // 3. Verificar que la base de datos esté lista
            $this->logger->info('Paso 3: Verificando que la BBDD del cliente esté disponible...');
            $host = $client->getHost();
            $port = 3306; // ✅ Puerto interno Docker, NO el publicado
            $username = $client->getDatabaseUserName();
            $password = $client->getDatabasePassword();
            $databaseName = $client->getDatabaseName();

            $this->logger->info('Parámetros de conexión:', [
                'host' => $host,
                'port' => $port,
                'username' => $username,
                'database' => $databaseName,
            ]);

            if (!$this->waitForDatabaseToBeReady($host, $port, $username, $password, $databaseName)) {
                throw new \Exception('No se pudo conectar a la base de datos del cliente después de múltiples intentos.');
            }

            // 4. Ejecutar migraciones del cliente
            $this->logger->info('Paso 4: Ejecutando migraciones del cliente...');
            $scriptPath = '/appdata/www/migrations/client/migrate_client.php';
            $clientIdentifier = $client->getUuidClient();
            $command = "php {$scriptPath} {$clientIdentifier}";

            $this->logger->info("Ejecutando comando de migraciones: {$command}");

            exec($command, $output, $returnVar);

            $this->logger->info('Resultado de migraciones:', [
                'return_code' => $returnVar,
                'output' => implode("\n", $output),
            ]);

            if (0 !== $returnVar) {
                $this->logger->error('Error al ejecutar migraciones del cliente: '.implode("\n", $output));
                throw new \Exception('Error al ejecutar migraciones del cliente');
            }

            $this->logger->info("✅ Cliente configurado exitosamente: {$client->getName()}");
            $this->logger->info('=== Proceso completado con éxito ===');

        } catch (ProcessFailedException $e) {
            $this->logger->error('ERROR al crear el contenedor Docker: '.$e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error('Error inesperado en el proceso: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
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
        // IMPORTANTE: Puerto interno de Docker, no el publicado
        $internalPort = 3306;

        $this->logger->info('Intentando conectar a la base de datos del cliente', [
            'host' => $host,
            'port_interno' => $internalPort,
            'database' => $databaseName,
        ]);

        $retries = 0;
        while ($retries < 10) {
            try {
                $pdo = new \PDO(
                    "mysql:host={$host};port={$internalPort};dbname={$databaseName};charset=utf8mb4",
                    $username,
                    $password,
                    [\PDO::ATTR_TIMEOUT => 3]
                );

                $this->logger->info('✅ Conexión exitosa a la base de datos del cliente');
                return true;
            } catch (\PDOException $e) {
                $this->logger->warning("Intento {$retries}/10: {$e->getMessage()}");
                ++$retries;
                sleep(5);
            }
        }

        $this->logger->error('❌ No se pudo conectar después de 10 intentos.');
        return false;
    }
}
