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
        $sql = '
        SELECT SUM(w.real_weight)
        FROM weights_log w
        INNER JOIN (
            SELECT scale_id, MAX(date) as max_date
            FROM weights_log
            WHERE product_id = :productId
            GROUP BY scale_id
        ) latest ON w.scale_id = latest.scale_id AND w.date = latest.max_date
        WHERE w.product_id = :productId2
    ';

        $result = $this->em->getConnection()
            ->executeQuery($sql, ['productId' => $productId, 'productId2' => $productId])
            ->fetchOne();

        return (float) ($result ?? 0);
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
