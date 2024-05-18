<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DatabaseConnectionManager
{
    private $defaultEntityManager;
    private $params;

    public function __construct(EntityManagerInterface $defaultEntityManager, ParameterBagInterface $params)
    {
        $this->defaultEntityManager = $defaultEntityManager;
        $this->params = $params;
    }

    public function getDefaultEntityManager(): EntityManagerInterface
    {
        return $this->defaultEntityManager;
    }

    public function getEntityManagerForClient(string $clientUuid): EntityManagerInterface
    {
        $connectionName = $this->getConnectionNameForClient($clientUuid);

        if (!$connectionName) {
            throw new \Exception('Cliente no reconocido');
        }

        $config = Setup::createAnnotationMetadataConfiguration([__DIR__."/../Client/Entity"], true);
        $connectionParams = [
            'url' => $this->params->get($connectionName)
        ];

        return EntityManager::create($connectionParams, $config);
    }

    private function getConnectionNameForClient(string $clientUuid): ?string
    {
        $conn = $this->defaultEntityManager->getConnection();
        $stmt = $conn->prepare('SELECT scheme FROM clients WHERE uuid = :uuid');
        $stmt->execute(['uuid' => $clientUuid]);
        $client = $stmt->fetch();

        return $client ? $client['scheme'] : null;
    }
}