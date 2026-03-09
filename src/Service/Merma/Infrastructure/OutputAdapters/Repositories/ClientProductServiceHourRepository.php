<?php

namespace App\Service\Merma\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\ProductServiceHour;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Repositorio de horas de servicio a nivel de producto.
 * Se instancia con el EntityManager del cliente (multi-tenant).
 */
final class ClientProductServiceHourRepository
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * @return ProductServiceHour[]
     */
    public function findByProductId(int $productId): array
    {
        return $this->em->createQuery(
            'SELECT h FROM App\Entity\Client\ProductServiceHour h WHERE IDENTITY(h.product) = :productId'
        )
            ->setParameter('productId', $productId)
            ->getResult();
    }

    public function save(ProductServiceHour $hour): void
    {
        $this->em->persist($hour);
        $this->em->flush();
    }

    public function deleteByProductId(int $productId): void
    {
        $this->em->createQuery(
            'DELETE FROM App\Entity\Client\ProductServiceHour h WHERE IDENTITY(h.product) = :productId'
        )
            ->setParameter('productId', $productId)
            ->execute();
    }
}
