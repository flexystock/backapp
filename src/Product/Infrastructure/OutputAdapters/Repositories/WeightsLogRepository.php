<?php

namespace App\Product\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\Product;
use App\Entity\Client\WeightsLog;
use App\Product\Application\OutputPorts\Repositories\WeightsLogRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class WeightsLogRepository implements WeightsLogRepositoryInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Obtiene la suma total del peso real (`real_weight`) de un producto.
     */
    public function getLatestTotalRealWeightByProduct(int $productId): float
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('SUM(w.real_weight)')
            ->from(WeightsLog::class, 'w')
            ->where('w.id IN (
            SELECT MAX(w2.id) 
            FROM App\Entity\Client\WeightsLog w2 
            WHERE w2.product = :productId 
            GROUP BY w2.scale
        )')
            ->setParameter('productId', $productId);

        return (float) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Obtiene todos los registros de pesaje (`WeightsLog`) de un cliente por UUID.
     */
    public function findAllByUuidClient(string $uuidClient): array
    {
        return $this->em->createQueryBuilder()
            ->select('w')
            ->from(WeightsLog::class, 'w')
            ->join('w.product', 'p')  // Unimos con "product"
            ->where('p.uuidClient = :uuidClient') // Filtramos por el cliente
            ->setParameter('uuidClient', $uuidClient)
            ->getQuery()
            ->getResult();
    }

    public function save(WeightsLog $weightsLog): void
    {
        $this->em->persist($weightsLog);
        $this->em->flush();
    }

    public function remove(WeightsLog $weightsLog): void
    {
        $this->em->remove($weightsLog);
        $this->em->flush();
    }
}
