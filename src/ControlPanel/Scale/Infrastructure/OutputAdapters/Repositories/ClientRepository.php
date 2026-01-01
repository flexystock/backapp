<?php

declare(strict_types=1);

namespace App\ControlPanel\Scale\Infrastructure\OutputAdapters\Repositories;

use App\ControlPanel\Scale\Application\OutputPorts\ClientRepositoryInterface;
use App\Entity\Main\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClientRepository extends ServiceEntityRepository implements ClientRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function findOneByUuid(string $uuid): ?Client
    {
        return $this->findOneBy(['uuid_client' => $uuid]);
    }

    public function findByUuids(array $uuids): array
    {
        if (empty($uuids)) {
            return [];
        }

        $clients = $this->createQueryBuilder('c')
            ->where('c.uuid_client IN (:uuids)')
            ->setParameter('uuids', $uuids)
            ->getQuery()
            ->getResult();

        // Index by UUID for easy lookup
        $indexed = [];
        foreach ($clients as $client) {
            $indexed[$client->getUuidClient()] = $client;
        }

        return $indexed;
    }
}
