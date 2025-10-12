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
        // Genera nombres/credenciales...
        $clientName = $client->getClientName();
        $uuid       = $client->getUuidClient();

        $containerName = $this->generateContainerName($clientName, $uuid);
        $volumeName    = $this->generateVolumeName($containerName);
        $databaseName  = $this->generateDatabaseName($clientName, $uuid);
        $user          = $this->generateDatabaseUser($clientName, $uuid);
        $password      = $this->generateRandomPassword();
        $port          = $this->findAvailablePort();
        $client->setPortBbdd($port);

        // Generar init.sql (en una ruta que exista en el HOST; ver punto 2)
        $initSqlPath = $this->buildHostInitPath($clientName, $uuid);
        $initSqlContent = "ALTER USER '{$user}'@'%' IDENTIFIED WITH mysql_native_password BY '{$password}';\nFLUSH PRIVILEGES;\n";
        if (false === @file_put_contents($initSqlPath, $initSqlContent)) {
            throw new \RuntimeException("No se pudo crear init.sql en $initSqlPath");
        }

        try {
            // 1) limpia si existe
            $this->removeExistingContainer($containerName);

            // 2) crea el contenedor
            $this->runDockerContainer($containerName, $volumeName, $databaseName, $user, $password, $port, $initSqlPath);

            // 3) espera a que esté listo (ping con backoff)
            $this->waitForMysql("127.0.0.1", $port, "root", "UZJIvESy5x");

            // 4) guarda en entidad
            $client->setDatabaseName($databaseName);
            $client->setDatabaseUserName($user);
            $client->setDatabasePassword($password);
            $client->setContainerName($containerName);
            $client->setHost($containerName);
            $client->setDockVolumeName($volumeName);

            return $client;
        } catch (\Throwable $e) {
            // No borres el volumen por fallos de conexión: podrías perder datos o el init incompleto
            $this->logger->error('Fallo creando DB cliente', ['ex' => $e]);
            // si quieres, solo intenta parar y limpiar el contenedor (no el volumen):
            try { $this->removeExistingContainer($containerName); } catch (\Throwable) {}
            throw $e;
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
            '/usr/bin/docker','run','-d',
            '--name',$containerName,
            '--network=docker-symfony-network',
            '--restart','always',
            '--label','com.flexystock=client-db',
            '--log-driver','json-file','--log-opt','max-size=10m','--log-opt','max-file=3',
            '--health-cmd','mysqladmin ping -h 127.0.0.1 -uroot -p"$MYSQL_ROOT_PASSWORD" || exit 1',
            '--health-interval','10s','--health-retries','12','--health-timeout','3s',
            '-p', $port.':3306',
            '--volume', $volumeName.':/var/lib/mysql',
            '--volume', $initSqlPath.':/docker-entrypoint-initdb.d/init.sql',
            '-e','MYSQL_DATABASE='.$databaseName,
            '-e','MYSQL_USER='.$user,
            '-e','MYSQL_PASSWORD='.$password,
            '-e','MYSQL_ROOT_PASSWORD=UZJIvESy5x',
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

    private function buildHostInitPath(string $clientName, string $uuid): string
    {
        $hostRoot = getenv('HOST_WORKDIR') ?: '/root/backapp'; // fallback razonable
        $dir = $hostRoot.'/var/DockerClients';
        if (!is_dir($dir)) { mkdir($dir, 0755, true); }
        return $dir.'/init_'.$clientName.'_'.substr($uuid, 0, 4).'.sql';
    }

    private function waitForMysql(string $host, int $port, string $user, string $password): void
    {
        $delays = [3,5,8,13,21,34,55]; // ~139s total
        foreach ($delays as $s) {
            // usa mysqladmin ping dentro del contenedor por puerto publicado
            $proc = new Process(['bash','-lc', "mysqladmin --connect-timeout=2 -h {$host} -P {$port} -u {$user} -p'{$password}' ping"]);
            $proc->run();
            if ($proc->isSuccessful()) { return; }
            sleep($s);
        }
        throw new \RuntimeException('MySQL no respondió a tiempo');
    }
}
