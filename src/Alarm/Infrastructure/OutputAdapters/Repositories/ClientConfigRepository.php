<?php

namespace App\Alarm\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\ClientConfig;
use Doctrine\ORM\EntityManagerInterface;

class ClientConfigRepository
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function findConfig(): ?ClientConfig
    {
        return $this->entityManager->getRepository(ClientConfig::class)->findOneBy([]);
    }

    public function save(ClientConfig $clientConfig): void
    {
        $this->entityManager->persist($clientConfig);
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
