<?php

namespace App\Service\Merma\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\MermaConfig;
use App\Entity\Client\MermaMonthlyReport;
use App\Entity\Client\ScaleEvent;
use App\Service\Merma\Application\OutputPorts\MermaConfigRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\MermaMonthlyReportRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\ScaleEventRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\ScaleReadingRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

// ═══════════════════════════════════════════════════════
// ScaleEventRepository
// ═══════════════════════════════════════════════════════

final class ScaleEventRepository extends ServiceEntityRepository implements ScaleEventRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScaleEvent::class);
    }

    public function save(ScaleEvent $event): void
    {
        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();
    }

    public function findById(int $id): ?ScaleEvent
    {
        return $this->find($id);
    }

    public function sumDeltaByType(
        int $scaleId,
        int $productId,
        string $type,
        \DateTimeInterface $from,
        \DateTimeInterface $to
    ): float {
        $result = $this->createQueryBuilder('e')
            ->select('SUM(e.deltaKg)')
            ->where('e.scaleId = :scaleId')
            ->andWhere('e.productId = :productId')
            ->andWhere('e.type = :type')
            ->andWhere('e.detectedAt BETWEEN :from AND :to')
            ->setParameter('scaleId', $scaleId)
            ->setParameter('productId', $productId)
            ->setParameter('type', $type)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    public function findActiveScaleProductPairsForMonth(\DateTimeInterface $month): array
    {
        $start = new \DateTime($month->format('Y-m-01') . ' 00:00:00');
        $end   = new \DateTime($month->format('Y-m-t')  . ' 23:59:59');

        return $this->createQueryBuilder('e')
            ->select('DISTINCT e.scaleId AS scaleId, e.productId AS productId')
            ->where('e.detectedAt BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getArrayResult();
    }

    public function findPendingAnomalies(int $scaleId, int $limit = 10): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.scaleId = :scaleId')
            ->andWhere('e.type = :type')
            ->andWhere('e.isConfirmed IS NULL')
            ->setParameter('scaleId', $scaleId)
            ->setParameter('type', ScaleEvent::TYPE_ANOMALIA)
            ->orderBy('e.detectedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function countPendingAnomalies(int $scaleId, int $productId): int
    {
        return (int) $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.scaleId = :scaleId')
            ->andWhere('e.productId = :productId')
            ->andWhere('e.type = :type')
            ->andWhere('e.isConfirmed IS NULL')
            ->setParameter('scaleId', $scaleId)
            ->setParameter('productId', $productId)
            ->setParameter('type', ScaleEvent::TYPE_ANOMALIA)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
