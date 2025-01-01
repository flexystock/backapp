<?php

namespace App\Service;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Main\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

// use Random\RandomException;

class DockerService
{
    private string $projectDir;
    private LoggerInterface $logger;
    private ClientRepositoryInterface $clientRepository;

    public function __construct(string $projectDir, LoggerInterface $logger, ClientRepositoryInterface $clientRepository)
    {
        $this->projectDir = $projectDir;
        $this->logger = $logger;
        $this->clientRepository = $clientRepository;
    }

    /**
     * @throws RandomException
     * @throws \Exception
     */
    public function createClientDatabase(Client $client): Client
    {
        try {
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

            // Generar el archivo init.sql con los valores del cliente
            $initSqlContent = "
                ALTER USER '{$user}'@'%' IDENTIFIED WITH mysql_native_password BY '{$password}';
                FLUSH PRIVILEGES;
                ";

            // Directorio donde se guardará el init.sql
            $dockerClientsDir = $this->projectDir.'/var/DockerClients';
            if (!is_dir($dockerClientsDir)) {
                mkdir($dockerClientsDir, 0755, true);
            }

            // Ruta completa al archivo init.sql
            $initSqlFileName = 'init_'.$clientName.'_'.substr($uuid, 0, 4).'.sql';
            $initSqlPath = $dockerClientsDir.'/'.$initSqlFileName;
            $this->logger->info('Ruta del archivo init.sql: '.$initSqlPath);

            // Guardar el contenido en el archivo
            $result = file_put_contents($initSqlPath, $initSqlContent);
            if (false === $result) {
                $this->logger->error('Error al escribir el archivo init.sql en '.$initSqlPath);
                throw new \Exception('No se pudo crear el archivo init.sql');
            }
            // Ejecutar comando Docker para crear el contenedor
            $this->runDockerContainer($containerName, $volumeName, $databaseName, $user, $password, $port, $initSqlPath);
            // Verificar y eliminar contenedor existente si es necesario
            $this->removeExistingContainer($containerName);

            // Ejecutar comando Docker para crear el contenedor
            $this->runDockerContainer($containerName, $volumeName, $databaseName, $user, $password, $port, $initSqlPath);

            // Actualizar el objeto Client con los nuevos datos
            $client->setDatabaseName($databaseName);
            $client->setDatabaseUserName($user);
            $client->setDatabasePassword($password);
            $client->setContainerName($containerName);
            $client->setHost($containerName);
            $client->setDockVolumeName($volumeName);

            return $client;
        } catch (\Exception $e) {
            // En caso de error, eliminar contenedor y volumen si existen
            $this->removeExistingContainer($containerName);
            $this->removeVolume($volumeName);

            throw $e; // Re-lanzar la excepción para que sea manejada por el llamador
        }
    }

    private function generateContainerName(string $clientName, string $uuid): string
    {
        // 1) Quitar o reemplazar espacios y caracteres no permitidos.
        //    Reemplazamos con subrayado `_`.
        $safeClientName = preg_replace('/[^A-Za-z0-9_.-]+/', '_', $clientName);

        // 2) Armar el nombre final
        return 'client_db_'.$safeClientName.'_'.substr($uuid, 0, 12);
    }

    private function generateVolumeName(string $containerName): string
    {
        return 'volume_'.$containerName;
    }

    private function generateDatabaseName(string $clientName, string $uuid): string
    {
        // 1) Quitar o reemplazar espacios y caracteres no permitidos.
        //    Reemplazamos con subrayado `_`.
        $safeClientName = preg_replace('/[^A-Za-z0-9_.-]+/', '_', $clientName);

        // 2) Armar el nombre final

        return 'client_db_'.$safeClientName.'_'.substr($uuid, 0, 4);
    }

    private function generateDatabaseUser(string $clientName, string $uuid): string
    {
        // 1) Quitar o reemplazar espacios y caracteres no permitidos.
        //    Reemplazamos con subrayado `_`.
        $safeClientName = preg_replace('/[^A-Za-z0-9_.-]+/', '_', $clientName);

        // 2) Armar el nombre final
        return 'client_user_'.$safeClientName.'_'.substr($uuid, 0, 4);
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
            '/usr/bin/docker', 'ps', '-a', '--filter', "name=$containerName", '--format', '{{.Names}}',
        ];
        $process = new Process($checkCommand);
        $process->run();

        if ($process->isSuccessful() && trim($process->getOutput()) === $containerName) {
            $removeCommand = [
                '/usr/bin/docker', 'rm', '-f', $containerName,
            ];
            $removeProcess = new Process($removeCommand);
            $removeProcess->run();

            if (!$removeProcess->isSuccessful()) {
                $this->logger->error('Error al eliminar el contenedor existente: '.$removeProcess->getErrorOutput());
                throw new ProcessFailedException($removeProcess);
            } else {
                $this->logger->info('Contenedor existente eliminado: '.$containerName);
            }
        }
    }

    private function runDockerContainer(string $containerName, string $volumeName, string $databaseName, string $user,
        string $password, int $port, string $initSqlPath): void
    {
        $command = [
            '/usr/bin/docker', 'run', '-d',
            '--name', $containerName,
            '--network=docker-symfony-network',
            '--restart', 'always',
            '-p', $port.':3306',
            '--volume', $volumeName.':/var/lib/mysql',
            '--volume', $initSqlPath.':/docker-entrypoint-initdb.d/init.sql',
            '-e', 'MYSQL_DATABASE='.$databaseName,
            '-e', 'MYSQL_USER='.$user,
            '-e', 'MYSQL_PASSWORD='.$password,
            '-e', 'MYSQL_ROOT_PASSWORD=UZJIvESy5x',
            'mysql:8.0',
        ];

        $this->logger->debug('Comando Docker: '.implode(' ', $command));
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->logger->error('Error al crear el contenedor Docker: '.$process->getErrorOutput());
            throw new ProcessFailedException($process);
        } else {
            $this->logger->info('Contenedor Docker creado exitosamente: '.$process->getOutput());
        }
    }

    /**
     * @throws \Exception
     */
    private function findAvailablePort(): int
    {
        $startPort = 40000;
        $endPort = 50000; // Puedes ajustar el rango según tus necesidades
        for ($port = $startPort; $port <= $endPort; ++$port) {
            if (!$this->isPortInUse($port)) {
                return $port;
            }
        }
        throw new \Exception('No hay puertos disponibles en el rango especificado.');
    }

    private function isPortInUse($port): bool
    {
        // 1. Verificar si el puerto está asignado a otro cliente en la base de datos
        $client = $this->clientRepository->findOneBy(['port_bbdd' => $port]);
        if (null !== $client) {
            return true;
        }

        // 2. Verificar si el puerto está en uso por algún proceso en el sistema
        $process = new Process(['lsof', '-i', 'tcp:'.$port]);
        $process->run();

        if ($process->isSuccessful() && !empty(trim($process->getOutput()))) {
            return true;
        }

        // 3. Verificar si el puerto está en uso por algún contenedor Docker
        $dockerProcess = new Process(['/usr/bin/docker', 'ps', '--format', '{{.Ports}}']);
        $dockerProcess->run();

        if ($dockerProcess->isSuccessful()) {
            $output = $dockerProcess->getOutput();
            if (str_contains($output, ':'.$port.'->')) {
                return true;
            }
        }

        return false;
    }

    private function removeVolume(string $volumeName): void
    {
        $removeCommand = [
            '/usr/bin/docker', 'volume', 'rm', '-f', $volumeName,
        ];
        $removeProcess = new Process($removeCommand);
        $removeProcess->run();

        if (!$removeProcess->isSuccessful()) {
            $this->logger->error('Error al eliminar el volumen existente: '.$removeProcess->getErrorOutput());
        // No lanzamos excepción aquí para no ocultar el error original
        } else {
            $this->logger->info('Volumen existente eliminado: '.$volumeName);
        }
    }
}
