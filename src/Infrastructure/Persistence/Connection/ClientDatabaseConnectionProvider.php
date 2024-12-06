<?php

namespace App\Infrastructure\Persistence\Connection;

use App\Client\Infrastructure\OutputAdapters\Repositories\ClientRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ClientDatabaseConnectionProvider
{
    private TokenStorageInterface $tokenStorage;
    private ?Connection $connection = null;
    private ClientRepository $clientRepository;

    public function __construct(TokenStorageInterface $tokenStorage, ClientRepository $clientRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->clientRepository = $clientRepository;
    }

    public function getConnection(): Connection
    {
        if ($this->connection) {
            return $this->connection;
        }

        $user = $this->security->getUser();

        if (!$user || !$user->getUuidClient()) {
            throw new \Exception('No client selected');
        }

        $uuidClient = $user->getUuidClient();

        // Obtener la configuración de la base de datos del cliente
        $clientConfig = $this->clientRepository->findOneBy(['uuidClient' => $uuidClient]);

        if (!$clientConfig) {
            throw new \Exception('Client configuration not found');
        }

        // Crear la conexión
        $this->connection = DriverManager::getConnection([
            'dbname' => $clientConfig->getDatabaseName(),
            'user' => $clientConfig->getDatabaseUserName(),
            'password' => $clientConfig->getDatabasePassword(),
            'host' => $clientConfig->getHost(),
            'port' => $clientConfig->getPortBbdd(),
            'driver' => 'pdo_mysql',
        ]);

        return $this->connection;
    }
}
