<?php

declare(strict_types=1);

namespace App\ControlPanel\Purchase\Infrastructure\OutputAdapters\Repositories;

use App\ControlPanel\Purchase\Application\OutputPorts\PurchaseScalesRepositoryInterface;
use App\Entity\Main\PurchaseScales;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseScalesRepository implements PurchaseScalesRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(PurchaseScales::class)
            ->findBy([], ['purchase_at' => 'DESC']);
    }

    public function findByUuidPurchase(string $uuidPurchase): ?PurchaseScales
    {
        return $this->entityManager->getRepository(PurchaseScales::class)
            ->findOneBy(['uuid_purchase' => $uuidPurchase]);
    }

    public function findByUuidClient(string $uuidClient): array
    {
        return $this->entityManager->getRepository(PurchaseScales::class)
            ->findBy(['uuid_client' => $uuidClient], ['purchase_at' => 'DESC']);
    }

    public function findByStatus(string $status): array
    {
        return $this->entityManager->getRepository(PurchaseScales::class)
            ->findBy(['status' => $status], ['purchase_at' => 'DESC']);
    }
}
