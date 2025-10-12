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
        $clientName   = $client->getClientName();
        $uuid         = $client->getUuidClient();

        $containerName = $this->generateContainerName($clientName, $uuid);
        $volumeName    = $this->generateVolumeName($containerName);
        $databaseName  = $this->generateDatabaseName($clientName, $uuid);
        $user          = $this->generateDatabaseUser($clientName, $uuid);
        $password      = $this->generateRandomPassword();
        $candidatePort = $this->findAvailablePort(); // candidato; NO lo guardamos aún

        // init.sql en host (para primer arranque de MySQL)
        $initSqlPath = $this->buildHostInitPath($clientName, $uuid);
        $initSqlContent = "ALTER USER '{$user}'@'%' IDENTIFIED WITH mysql_native_password BY '{$password}';\nFLUSH PRIVILEGES;\n";
        if (false === @file_put_contents($initSqlPath, $initSqlContent)) {
            throw new \RuntimeException("No se pudo crear init.sql en $initSqlPath");
        }

        try {
            // --- Reusar si existe ---
            if ($this->containerExists($containerName)) {
                $this->logger->info("El contenedor $containerName ya existe");

                if ($this->containerIsRunning($containerName)) {
                    if ($this->containerIsHealthy($containerName)) {
                        $this->logger->info("$containerName está running + healthy, se reutiliza.");
                    } else {
                        $this->logger->info("$containerName running pero no healthy, esperamos health...");
                        $this->waitForMysqlByHealth($containerName);
                    }
                } else {
                    $this->logger->info("$containerName existe pero está parado, se hace start.");
                    $this->startContainer($containerName);
                    $this->waitForMysqlByHealth($containerName);
                }

                // asegura usuario/privilegios (por si init.sql no se aplicó)
                $this->ensureUserAndGrants($containerName, $databaseName, $user, $password);
            } else {
                // No existe: se crea
                $this->runDockerContainer($containerName, $volumeName, $databaseName, $user, $password, $candidatePort, $initSqlPath);
                $this->waitForMysqlByHealth($containerName);
                $this->ensureUserAndGrants($containerName, $databaseName, $user, $password);
            }

            // Puerto REAL publicado por Docker para 3306/tcp
            $publishedPort = $this->getPublishedPort($containerName);

            // Persistimos datos finales en la entidad
            $client->setDatabaseName($databaseName);
            $client->setDatabaseUserName($user);
            $client->setDatabasePassword($password);
            $client->setContainerName($containerName);
            $client->setHost($containerName);          // para acceso interno por red docker
            $client->setDockVolumeName($volumeName);
            $client->setPortBbdd((int)$publishedPort); // para acceso externo (Workbench)

            // (opcional) lanzar migraciones solo de este cliente
            $this->runMigrationsForClient($client);

            return $client;

        } catch (\Throwable $e) {
            $this->logger->error('Fallo creando DB cliente', ['ex' => $e]);
            // NO borrar volumen ni rm -f aquí.
            throw $e;
        }
    }

    private function generateContainerName(string $clientName, string $uuid): string
    {
        $safeClientName = preg_replace('/[^A-Za-z0-9_.-]+/', '_', $clientName);
        return 'client_db_'.$safeClientName.'_'.substr($uuid, 0, 12);
    }

    private function generateVolumeName(string $containerName): string
    {
        return 'volume_'.$containerName;
    }

    private function generateDatabaseName(string $clientName, string $uuid): string
    {
        $safeClientName = preg_replace('/[^A-Za-z0-9_.-]+/', '_', $clientName);
        return 'client_db_'.$safeClientName.'_'.substr($uuid, 0, 4);
    }

    private function generateDatabaseUser(string $clientName, string $uuid): string
    {
        $safeClientName = preg_replace('/[^A-Za-z0-9_.-]+/', '_', $clientName);
        return 'client_user_'.$safeClientName.'_'.substr($uuid, 0, 4);
    }

    /**
     * @throws RandomException
     */
    private function generateRandomPassword(): string
    {
        return bin2hex(random_bytes(8));
    }

    private function removeExistingContainer(string $containerName): void
    {
        if (!$this->containerExists($containerName)) {
            return;
        }

        if ($this->containerIsRunning($containerName)) {
            $this->logger->warning("removeExistingContainer: $containerName está RUNNING, no se borra.");
            return;
        }

        $rm = new Process(['/usr/bin/docker','rm','-f',$containerName]);
        $rm->run();
        if (!$rm->isSuccessful()) {
            $this->logger->error('Error al eliminar contenedor: '.$rm->getErrorOutput());
            throw new ProcessFailedException($rm);
        }
        $this->logger->info("Contenedor eliminado: $containerName");
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
     * Lee el puerto REAL publicado por Docker (HostPort) para 3306/tcp.
     */
    private function getPublishedPort(string $containerName): int
    {
        $fmt = '{{ (index (index .NetworkSettings.Ports "3306/tcp") 0).HostPort }}';
        $p = new Process(['/usr/bin/docker', 'inspect', '--format', $fmt, $containerName]);
        $p->run();

        if (!$p->isSuccessful()) {
            $this->logger->error('No se pudo inspeccionar el puerto publicado', [
                'container' => $containerName,
                'stderr' => $p->getErrorOutput(),
            ]);
            return 3306; // fallback
        }

        $port = trim($p->getOutput());
        if ($port === '' || !ctype_digit($port)) {
            $this->logger->warning('Valor de HostPort inesperado', [
                'container' => $containerName,
                'raw' => $port,
            ]);
            return 3306;
        }
        return (int)$port;
    }

    /**
     * @throws \Exception
     */
    private function findAvailablePort(): int
    {
        $startPort = 40000;
        $endPort = 50000;
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
            $proc = new Process(['bash','-lc', "mysqladmin --connect-timeout=2 -h {$host} -P {$port} -u {$user} -p'{$password}' ping"]);
            $proc->run();
            if ($proc->isSuccessful()) { return; }
            sleep($s);
        }
        throw new \RuntimeException('MySQL no respondió a tiempo');
    }

    private function waitForMysqlByHealth(string $containerName): void {
        $retries = 60;
        while ($retries-- > 0) {
            $p = new Process(['/usr/bin/docker','inspect','--format','{{.State.Health.Status}}', $containerName]);
            $p->run();
            if ($p->isSuccessful()) {
                $status = trim($p->getOutput());
                if ($status === 'healthy') return;
            }
            sleep(5);
        }
        throw new \RuntimeException('MySQL no llegó a healthy a tiempo');
    }

    private function containerExists(string $name): bool {
        $p = new Process(['/usr/bin/docker','ps','-a','--filter',"name=$name",'--format','{{.Names}}']);
        $p->run();
        return $p->isSuccessful() && trim($p->getOutput()) === $name;
    }

    private function containerIsRunning(string $name): bool {
        $p = new Process(['/usr/bin/docker','inspect','--format','{{.State.Running}}', $name]);
        $p->run();
        return $p->isSuccessful() && trim($p->getOutput()) === 'true';
    }

    private function containerIsHealthy(string $name): bool {
        $p = new Process(['/usr/bin/docker','inspect','--format','{{.State.Health.Status}}', $name]);
        $p->run();
        return $p->isSuccessful() && trim($p->getOutput()) === 'healthy';
    }

    private function startContainer(string $name): void {
        $p = new Process(['/usr/bin/docker','start',$name]);
        $p->run();
        if (!$p->isSuccessful()) {
            throw new ProcessFailedException($p);
        }
    }

    private function ensureUserAndGrants(string $containerName, string $dbName, string $user, string $pass): void
    {
        $sql = <<<SQL
CREATE USER IF NOT EXISTS '$user'@'%' IDENTIFIED WITH mysql_native_password BY '$pass';
ALTER USER '$user'@'%' IDENTIFIED WITH mysql_native_password BY '$pass';
GRANT ALL PRIVILEGES ON `$dbName`.* TO '$user'@'%';
FLUSH PRIVILEGES;
SQL;

        $proc = new Process([
            '/usr/bin/docker','exec','-i',$containerName,
            'mysql','-uroot','-pUZJIvESy5x','-e',$sql
        ]);
        $proc->run();
        if (!$proc->isSuccessful()) {
            throw new \RuntimeException('No se pudo asegurar usuario/permisos: '.$proc->getErrorOutput());
        }
    }

    private function ensureClientUser(string $containerName, string $dbName, string $user, string $password): void
    {
        $sql = <<<SQL
ALTER USER '{$user}'@'%' IDENTIFIED WITH mysql_native_password BY '{$password}';
GRANT ALL PRIVILEGES ON `{$dbName}`.* TO '{$user}'@'%';
FLUSH PRIVILEGES;
SQL;

        $cmd = [
            '/usr/bin/docker','exec','-i',$containerName,
            'mysql','-uroot','-pUZJIvESy5x','-e',$sql
        ];

        $p = new Process($cmd);
        $p->run();

        if (!$p->isSuccessful()) {
            $this->logger->error('ensureClientUser falló', [
                'container' => $containerName,
                'db'        => $dbName,
                'user'      => $user,
                'stderr'    => $p->getErrorOutput(),
            ]);
        } else {
            $this->logger->info('Usuario/privilegios del cliente asegurados', [
                'container' => $containerName,
                'db'        => $dbName,
                'user'      => $user,
            ]);
        }
    }

    private function runMigrationsForClient(Client $client): void
    {
        $script = $this->projectDir . '/migrations/client/migrate_client.php';
        $identifier = $client->getUuidClient() ?: $client->getDatabaseName();

        $proc = new Process(['php', $script, $identifier], $this->projectDir);
        $proc->setTimeout(600);
        $proc->run();

        if (!$proc->isSuccessful()) {
            $this->logger->error('Migraciones cliente fallaron', [
                'client' => $identifier,
                'stdout' => $proc->getOutput(),
                'stderr' => $proc->getErrorOutput(),
            ]);
            // Si quieres romper el alta:
            // throw new ProcessFailedException($proc);
        } else {
            $this->logger->info('Migraciones cliente OK', [
                'client' => $identifier,
                'output' => $proc->getOutput(),
            ]);
        }
    }
}