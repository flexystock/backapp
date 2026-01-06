<?php

declare(strict_types=1);

namespace App\Scales\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Main\PurchaseScales;
use App\Scales\Application\OutputPorts\PurchaseScalesRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseScalesRepository implements PurchaseScalesRepositoryInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save(PurchaseScales $purchaseScales): void
    {
        $this->entityManager->persist($purchaseScales);
        $this->entityManager->flush();
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
}
