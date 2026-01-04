<?php

declare(strict_types=1);

namespace App\ControlPanel\Purchase\Infrastructure\OutputAdapters\Repositories;

use App\ControlPanel\Purchase\Application\OutputPorts\ClientRepositoryInterface;
use App\Entity\Main\Client;
use Doctrine\ORM\EntityManagerInterface;

class ClientRepository implements ClientRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findByUuid(string $uuidClient): ?Client
    {
        return $this->entityManager->getRepository(Client::class)
            ->findOneBy(['uuid_client' => $uuidClient]);
    }
}
