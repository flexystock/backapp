<?php

namespace App\Product\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\ProductSupplier;
use App\Product\Application\OutputPorts\Repositories\ProductSupplierRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class ProductSupplierRepository implements ProductSupplierRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findById(int $id): ?ProductSupplier
    {
        return $this->em->getRepository(ProductSupplier::class)->find($id);
    }

    public function findByProductId(int $productId): array
    {
        return $this->em->getRepository(ProductSupplier::class)->findBy(['productId' => $productId]);
    }

    public function findByProductIdAndClientSupplierId(int $productId, int $clientSupplierId): ?ProductSupplier
    {
        return $this->em->getRepository(ProductSupplier::class)->findOneBy([
            'productId' => $productId,
            'clientSupplierId' => $clientSupplierId
        ]);
    }

    public function findPreferredByProductId(int $productId): ?ProductSupplier
    {
        return $this->em->getRepository(ProductSupplier::class)->findOneBy([
            'productId' => $productId,
            'isPreferred' => true
        ]);
    }

    public function save(ProductSupplier $productSupplier): void
    {
        $productSupplier->setUpdatedAt(new \DateTime());
        $this->em->persist($productSupplier);
        $this->em->flush();
    }

    public function remove(ProductSupplier $productSupplier): void
    {
        $this->em->remove($productSupplier);
        $this->em->flush();
    }
}
