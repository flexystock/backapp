<?php

namespace App\Service;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;

class ClientEntityManagerFactory
{
    /**
     * Crea un EntityManager para un cliente específico.
     *
     * @param array $connectionParams parámetros de conexión para Doctrine
     */
    public function createEntityManagerForClient(array $connectionParams): EntityManagerInterface
    {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [__DIR__.'/../../Entity/Client'], // Ruta correcta a tus entidades
            isDevMode: true // Cambia a false en producción
        );

        $connection = DriverManager::getConnection($connectionParams, $config);

        return new EntityManager($connection, $config);
    }
}
