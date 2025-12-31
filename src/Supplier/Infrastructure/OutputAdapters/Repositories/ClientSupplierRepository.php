<?php

namespace App\Supplier\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\ClientSupplier;
use App\Supplier\Application\OutputPorts\Repositories\ClientSupplierRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class ClientSupplierRepository implements ClientSupplierRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findAll(): array
    {
        return $this->entityManager
            ->getRepository(ClientSupplier::class)
            ->findAll();
    }

    public function findById(int $id): ?ClientSupplier
    {
        return $this->entityManager
            ->getRepository(ClientSupplier::class)
            ->find($id);
    }

    public function findBySupplierId(int $supplierId): ?ClientSupplier
    {
        return $this->entityManager
            ->getRepository(ClientSupplier::class)
            ->findOneBy(['supplierId' => $supplierId]);
    }

    public function save(ClientSupplier $clientSupplier): void
    {
        $this->entityManager->persist($clientSupplier);
        $this->entityManager->flush();
    }

    public function delete(ClientSupplier $clientSupplier): void
    {
        $this->entityManager->remove($clientSupplier);
        $this->entityManager->flush();
    }
}
