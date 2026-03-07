<?php

namespace App\Service\Merma\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\Scales;
use App\Service\Merma\Application\OutputPorts\GetScalesByProductRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Client-specific scales repository for the Merma module.
 * Instantiated with the client's EntityManager (multi-tenant).
 */
final class ClientScalesRepository implements GetScalesByProductRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function findAllByProductId(int $productId): array
    {
        return $this->em->createQueryBuilder()
            ->select('s')
            ->from(Scales::class, 's')
            ->where('IDENTITY(s.product_id) = :productId')
            ->setParameter('productId', $productId)
            ->getQuery()
            ->getResult();
    }
}
