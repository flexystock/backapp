<?php

namespace App\Supplier\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Main\Supplier;
use App\Supplier\Application\OutputPorts\Repositories\SupplierRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class SupplierRepository implements SupplierRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findById(int $id): ?Supplier
    {
        return $this->em->getRepository(Supplier::class)->find($id);
    }

    public function findBySlug(string $slug): ?Supplier
    {
        return $this->em->getRepository(Supplier::class)->findOneBy(['slug' => $slug]);
    }

    public function findAll(): array
    {
        return $this->em->getRepository(Supplier::class)->findAll();
    }

    public function findActive(): array
    {
        return $this->em->getRepository(Supplier::class)->findBy(['is_active' => true]);
    }

    public function save(Supplier $supplier): void
    {
        $this->em->persist($supplier);
        $this->em->flush();
    }

    public function remove(Supplier $supplier): void
    {
        $this->em->remove($supplier);
        $this->em->flush();
    }
}
