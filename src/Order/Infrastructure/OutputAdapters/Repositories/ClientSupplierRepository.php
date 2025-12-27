<?php

namespace App\Order\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\ClientSupplier;
use App\Order\Application\OutputPorts\Repositories\ClientSupplierRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class ClientSupplierRepository implements ClientSupplierRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findById(int $id): ?ClientSupplier
    {
        return $this->em->getRepository(ClientSupplier::class)->find($id);
    }

    public function findBySupplierId(int $supplierId): ?ClientSupplier
    {
        return $this->em->getRepository(ClientSupplier::class)->findOneBy(['supplierId' => $supplierId]);
    }

    public function findAll(): array
    {
        return $this->em->getRepository(ClientSupplier::class)->findAll();
    }

    public function findActive(): array
    {
        return $this->em->getRepository(ClientSupplier::class)->findBy(['isActive' => true]);
    }

    public function save(ClientSupplier $clientSupplier): void
    {
        $clientSupplier->setUpdatedAt(new \DateTime());
        $this->em->persist($clientSupplier);
        $this->em->flush();
    }

    public function remove(ClientSupplier $clientSupplier): void
    {
        $this->em->remove($clientSupplier);
        $this->em->flush();
    }
}
