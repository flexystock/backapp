<?php

// src/Infrastructure/Services/ClientConnectionManager.php

namespace App\Infrastructure\Services;

use App\Entity\Main\Client;
use Doctrine\DBAL\Configuration as DBALConfiguration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\DefaultSchemaManagerFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ClientConnectionManager
{
    private EntityManagerInterface $mainEntityManager;
    private LoggerInterface $logger;
    private ParameterBagInterface $params;

    /**
     * Almacena en caché los EntityManagers para evitar recrearlos.
     *
     * @var array<string, EntityManagerInterface>
     */
    private array $entityManagersCache = [];

    public function __construct(
        EntityManagerInterface $mainEntityManager,
        LoggerInterface $logger,
        ParameterBagInterface $params,
    ) {
        $this->mainEntityManager = $mainEntityManager;
        $this->logger = $logger;
        $this->params = $params;
    }

    /**
     * Obtiene el EntityManager para un cliente específico.
     *
     * @throws \Exception
     */
    public function getEntityManager(string $uuidClient): EntityManagerInterface
    {
        // Verificar si el EntityManager ya está en caché
        if (isset($this->entityManagersCache[$uuidClient])) {
            $this->logger->info("ClientConnectionManager: Usando EntityManager en caché para cliente '$uuidClient'.");

            return $this->entityManagersCache[$uuidClient];
        }

        // Obtener la entidad Client desde la base de datos main
        /** @var Client|null $client */
        $client = $this->mainEntityManager->getRepository(Client::class)->find($uuidClient);

        if (!$client) {
            $this->logger->error("ClientConnectionManager: Client with UUID '$uuidClient' not found.");
            throw new \Exception("Client with UUID '$uuidClient' not found.");
        }

        // Construir los parámetros de conexión para Doctrine
        $connectionParams = [
            'dbname' => $client->getDatabaseName(),
            'user' => $client->getDatabaseUserName(),
            'password' => $client->getDatabasePassword(),
            'host' => $client->getHost(),
            'port' => $this->getDatabasePort($client->getHost()),
            'driver' => 'pdo_mysql', // Ajusta según tu base de datos
            'charset' => 'utf8mb4',
        ];
        // Log de los parámetros de conexión (omitimos la contraseña por seguridad)
        $this->logger->info("ClientConnectionManager: Parámetros de conexión para cliente '$uuidClient':", [
            'dbname' => $connectionParams['dbname'],
            'user' => $connectionParams['user'],
            'host' => $connectionParams['host'],
            'port' => $connectionParams['port'],
        ]);
        $this->logger->info("ClientConnectionManager: Creating EntityManager for client UUID '$uuidClient'.");

        // Configuración de Doctrine ORM
        $proxyDir = __DIR__.'/../../../../var/doctrine/proxies';
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [__DIR__.'/../../../../Entity/Client'], // Ruta a las entidades del cliente
            isDevMode: true, // Cambia a true en desarrollo
            proxyDir: $proxyDir,
            cache: null,
            // useSimpleAnnotationReader: false,
        );
        $absolutePath = __DIR__.'/../../../../Entity/Client';
        $this->logger->info('absolutePath', [
            'absolutePath' => $absolutePath,
        ]);
        // Configuración de DBAL
        $dbalConfig = new DBALConfiguration();
        // Configurar el Schema Manager Factory
        $schemaManagerFactory = new DefaultSchemaManagerFactory();
        $dbalConfig->setSchemaManagerFactory($schemaManagerFactory);
        // die("llegamos hasta aqui");
        try {
            // Crear la conexión
            $connection = DriverManager::getConnection($connectionParams, $dbalConfig);

            // Crear el EntityManager utilizando el constructor
            $entityManager = new EntityManager($connection, $config);

            // Almacenar en caché
            $this->entityManagersCache[$uuidClient] = $entityManager;
            $this->logger->info("ClientConnectionManager: EntityManager creado y almacenado en caché para cliente '$uuidClient'.");

            // die("llegamos hasta aqui2");
            return $entityManager;
        } catch (\Exception $e) {
            $this->logger->error("ClientConnectionManager: Error creating EntityManager for client UUID '$uuidClient'.", [
                'exception' => $e,
            ]);
            throw new \Exception("Error creating EntityManager for client UUID '$uuidClient'.");
        }
    }

    /**
     * Determina el puerto correcto para la base de datos según el host.
     */
    private function getDatabasePort(string $host): int
    {
        // Si el host es 'localhost' o '127.0.0.1', usa el puerto mapeado (40001)
        // Si es otro host (nombre del contenedor), usa el puerto interno (3306)
        if ('localhost' === $host || '127.0.0.1' === $host) {
            // Asume que el puerto está mapeado en el cliente
            // Puedes obtener este valor de una configuración o variable de entorno si es necesario
            return 40001;
        }

        // Para conexiones internas en Docker, usa el puerto interno de MySQL
        return 3306;
    }

    /**
     * Obtiene el EntityManager para la base de datos central (usada por el CRON).
     *
     * @throws \Exception
     */
    public function getCentralEntityManager(): EntityManagerInterface
    {
        // Definir directamente los parámetros de conexión para la BBDD central.
        $connectionParams = [
            'dbname' => 'central_database',             // Nombre de la base de datos central
            'user' => 'user',                         // Usuario
            'password' => 'password',                     // Contraseña
            'host' => 'docker-symfony-dbCentral',     // Host (puede ser el nombre del contenedor)
            'port' => 3306,                           // Puerto (3306 para MySQL interno)
            'driver' => 'pdo_mysql',
            'charset' => 'utf8mb4',
        ];

        $this->logger->info('ClientConnectionManager: Creando Central EntityManager con parámetros hardcodeados.');

        // Configuración de Doctrine ORM para las entidades centrales (en src/Entity/Cron)
        $proxyDir = __DIR__.'/../../../../var/doctrine/proxies';
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [__DIR__.'/../../../../Entity/Cron'], // Directorio de entidades para la BBDD central.
            isDevMode: true,
            proxyDir: $proxyDir,
            cache: null
        );

        $dbalConfig = new DBALConfiguration();
        $schemaManagerFactory = new DefaultSchemaManagerFactory();
        $dbalConfig->setSchemaManagerFactory($schemaManagerFactory);

        try {
            // Crear la conexión
            $connection = DriverManager::getConnection($connectionParams, $dbalConfig);
            // Crear el EntityManager usando la configuración de ORM para las entidades centrales
            $entityManager = new EntityManager($connection, $config);
            $this->logger->info('ClientConnectionManager: Central EntityManager creado correctamente.');

            return $entityManager;
        } catch (\Exception $e) {
            $this->logger->error('ClientConnectionManager: Error creando Central EntityManager.', ['exception' => $e]);
            throw new \Exception('Error creando Central EntityManager.');
        }
    }
}
