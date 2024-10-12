<?php
namespace App\Service;

use App\Entity\Main\Client;
use Random\RandomException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Psr\Log\LoggerInterface;
use App\Client\Infrastructure\OutputPorts\ClientRepositoryInterface;


class DockerService
{
    private LoggerInterface $logger;
    private ClientRepositoryInterface $clientRepository;

    public function __construct(LoggerInterface $logger, ClientRepositoryInterface $clientRepository)
    {
        $this->logger = $logger;
        $this->clientRepository = $clientRepository;
    }

    /**
     * @throws RandomException
     * @throws \Exception
     */
    public function createClientDatabase(Client $client): Client
    {
        // Generar nombres y credenciales
        $clientName = $client->getClientName();
        $uuid = $client->getUuidClient();

        $containerName = $this->generateContainerName($clientName, $uuid);
        $volumeName = $this->generateVolumeName($containerName);
        $databaseName = $this->generateDatabaseName($clientName, $uuid);
        $user = $this->generateDatabaseUser($clientName, $uuid);
        $password = $this->generateRandomPassword();

        // Asignar puerto disponible
        $port = $this->findAvailablePort();
        $client->setPortBbdd($port);

        // Verificar y eliminar contenedor existente si es necesario
        $this->removeExistingContainer($containerName);

        // Ejecutar comando Docker para crear el contenedor
        $this->runDockerContainer($containerName, $volumeName, $databaseName, $user, $password, $port);

        // Actualizar el objeto Client con los nuevos datos
        $client->setDatabaseName($databaseName);
        $client->setDatabaseUserName($user);
        $client->setDatabasePassword($password);
        $client->setContainerName($containerName);
        $client->setHost($containerName);
        $client->setDockVolumeName($volumeName);

        return $client;
    }
    private function generateContainerName(string $clientName, string $uuid): string
    {
        return 'client_db_' . $clientName . '_' . substr($uuid, 0, 12);
    }

    private function generateVolumeName(string $containerName): string
    {
        return 'volume_' . $containerName;
    }

    private function generateDatabaseName(string $clientName, string $uuid): string
    {
        return 'client_db_' . $clientName . '_' . substr($uuid, 0, 4);
    }

    private function generateDatabaseUser(string $clientName, string $uuid): string
    {
        return 'client_user_' . $clientName . '_' . substr($uuid, 0, 4);
    }

    /**
     * @throws RandomException
     */
    private function generateRandomPassword(): string
    {
        return bin2hex(random_bytes(8)); // Genera una contraseña segura
    }

    private function removeExistingContainer(string $containerName): void
    {
        $checkCommand = [
            '/usr/bin/docker', 'ps', '-a', '--filter', "name=$containerName", '--format', '{{.Names}}'
        ];
        $process = new Process($checkCommand);
        $process->run();

        if ($process->isSuccessful() && trim($process->getOutput()) === $containerName) {
            $removeCommand = [
                '/usr/bin/docker', 'rm', '-f', $containerName
            ];
            $removeProcess = new Process($removeCommand);
            $removeProcess->run();

            if (!$removeProcess->isSuccessful()) {
                $this->logger->error('Error al eliminar el contenedor existente: ' . $removeProcess->getErrorOutput());
                throw new ProcessFailedException($removeProcess);
            } else {
                $this->logger->info('Contenedor existente eliminado: ' . $containerName);
            }
        }
    }

    private function runDockerContainer(string $containerName, string $volumeName,string $databaseName, string $user,
                                        string $password, int $port): void
    {
        $command = [
            '/usr/bin/docker', 'run', '-d',
            '--name', $containerName,
            '--network=docker-symfony-network',
            '--restart', 'always',
            '-p', $port . ':3306',
            '--volume', $volumeName . ':/var/lib/mysql',  // Docker gestionará el volumen
            '-e', 'MYSQL_DATABASE=' . $databaseName,
            '-e', 'MYSQL_USER=' . $user,
            '-e', 'MYSQL_PASSWORD=' . $password,
            '-e', 'MYSQL_ROOT_PASSWORD=UZJIvESy5x',
            'mysql:8.0'
        ];

        $this->logger->debug('Comando Docker: ' . implode(' ', $command));
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->logger->error('Error al crear el contenedor Docker: ' . $process->getErrorOutput());
            throw new ProcessFailedException($process);
        } else {
            $this->logger->info('Contenedor Docker creado exitosamente: ' . $process->getOutput());
        }
    }



    /**
     * @throws \Exception
     */
    private function findAvailablePort(): int
    {
        $port = 40015;
        while ($port <= 65535) {
            if (!$this->isPortInUse($port)) {
                return $port;
            }
            $port++;
        }
        throw new \Exception('No hay puertos disponibles.');
    }

    private function isPortInUse($port): bool
    {
        // 1. Verificar si el puerto está asignado a otro cliente en la base de datos
        $client = $this->clientRepository->findOneBy(['port_bbdd' => $port]);
        if ($client !== null) {
            return true;
        }

        // 2. Verificar si el puerto está en uso en el sistema
        $connection = @fsockopen('localhost', $port);
        if (is_resource($connection)) {
            fclose($connection);
            return true;
        }
        return false;
    }
}