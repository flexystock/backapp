<?php

declare(strict_types=1);

namespace App\Client\Infrastructure\OutputAdapters\Repositories;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Main\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ClientRepository extends ServiceEntityRepository implements ClientRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($registry, Client::class);
    }

    public function save(Client $client): void
    {
        $this->entityManager->persist($client);
        $this->entityManager->flush();
    }

    public function findByUuid(string $uuid): ?Client
    {
        return $this->findOneBy(['uuid_client' => $uuid]);
    }

    public function findByName(string $name): ?Client
    {
        return $this->findOneBy(['client_name' => $name]);
    }

    public function findOneByPort(int $port): ?Client
    {
        return $this->findOneBy(['port' => $port]);
    }
}
