<?php

namespace App\Service\Merma\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\ScaleEvent;
use App\Service\Merma\Application\OutputPorts\GetPendingAnomaliesRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\ScaleEventRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Implementación real del repositorio de eventos de balanza.
 * Se instancia con el EntityManager del cliente (multi-tenant).
 */
final class ClientScaleEventRepository implements ScaleEventRepositoryInterface, GetPendingAnomaliesRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function save(ScaleEvent $event): void
    {
        $this->em->persist($event);
        $this->em->flush();
    }

    public function findById(int $id): ?ScaleEvent
    {
        return $this->em->getRepository(ScaleEvent::class)->find($id);
    }

    public function sumDeltaByType(
        int $scaleId,
        int $productId,
        string $type,
        \DateTimeInterface $from,
        \DateTimeInterface $to
    ): float {
        $result = $this->em->createQuery(
            'SELECT SUM(e.deltaKg)
             FROM App\Entity\Client\ScaleEvent e
             WHERE IDENTITY(e.scale) = :scaleId
               AND IDENTITY(e.product) = :productId
               AND e.type = :type
               AND e.detectedAt >= :from
               AND e.detectedAt <= :to'
        )
            ->setParameter('scaleId', $scaleId)
            ->setParameter('productId', $productId)
            ->setParameter('type', $type)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getSingleScalarResult();

        return (float) ($result ?? 0.0);
    }

    public function findActiveScaleProductPairsForMonth(\DateTimeInterface $month): array
    {
        $start = new \DateTime($month->format('Y-m-01') . ' 00:00:00');
        $end   = new \DateTime($month->format('Y-m-t') . ' 23:59:59');

        $rows = $this->em->createQuery(
            'SELECT IDENTITY(e.scale) AS scaleId, IDENTITY(e.product) AS productId
             FROM App\Entity\Client\ScaleEvent e
             WHERE e.detectedAt >= :start AND e.detectedAt <= :end
             GROUP BY IDENTITY(e.scale), IDENTITY(e.product)'
        )
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getArrayResult();

        return array_map(fn(array $r) => [
            'scaleId'   => (int) $r['scaleId'],
            'productId' => (int) $r['productId'],
        ], $rows);
    }

    public function findPendingAnomalies(int $scaleId, int $limit = 10): array
    {
        return $this->em->createQuery(
            'SELECT e
             FROM App\Entity\Client\ScaleEvent e
             WHERE IDENTITY(e.scale) = :scaleId
               AND e.type = :type
               AND e.isConfirmed IS NULL
             ORDER BY e.detectedAt DESC'
        )
            ->setParameter('scaleId', $scaleId)
            ->setParameter('type', ScaleEvent::TYPE_ANOMALIA)
            ->setMaxResults($limit)
            ->getResult();
    }

    public function countPendingAnomalies(int $scaleId, int $productId): int
    {
        $result = $this->em->createQuery(
            'SELECT COUNT(e.id)
             FROM App\Entity\Client\ScaleEvent e
             WHERE IDENTITY(e.scale) = :scaleId
               AND IDENTITY(e.product) = :productId
               AND e.type = :type
               AND e.isConfirmed IS NULL'
        )
            ->setParameter('scaleId', $scaleId)
            ->setParameter('productId', $productId)
            ->setParameter('type', ScaleEvent::TYPE_ANOMALIA)
            ->getSingleScalarResult();

        return (int) $result;
    }

    public function findAllPendingAnomalies(): array
    {
        return $this->em->createQuery(
            'SELECT e
             FROM App\Entity\Client\ScaleEvent e
             WHERE e.type = :type
               AND e.isConfirmed IS NULL
             ORDER BY e.detectedAt DESC'
        )
            ->setParameter('type', ScaleEvent::TYPE_ANOMALIA)
            ->getResult();
    }

    /**
     * Returns all events (reposicion, consumo, anomalia) for a given scale and product
     * within the specified date range, ordered chronologically ascending.
     */
    public function findTimelineEvents(int $scaleId, int $productId, \DateTime $from, \DateTime $to): array
    {
        return $this->em->createQuery(
            'SELECT e
             FROM App\Entity\Client\ScaleEvent e
             WHERE IDENTITY(e.scale) = :scaleId
               AND IDENTITY(e.product) = :productId
               AND e.detectedAt >= :from
               AND e.detectedAt <= :to
             ORDER BY e.detectedAt ASC'
        )
            ->setParameter('scaleId', $scaleId)
            ->setParameter('productId', $productId)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getResult();
    }

    /**
     * Returns resolved anomalies (where isConfirmed is not null) for a given scale and product,
     * optionally filtered by date range, ordered by detectedAt descending.
     */
    public function findResolvedAnomalies(int $scaleId, int $productId, ?\DateTime $from, ?\DateTime $to): array
    {
        $dql = 'SELECT e
                FROM App\Entity\Client\ScaleEvent e
                WHERE IDENTITY(e.scale) = :scaleId
                  AND IDENTITY(e.product) = :productId
                  AND e.type = :type
                  AND e.isConfirmed IS NOT NULL';

        if ($from !== null) {
            $dql .= ' AND e.detectedAt >= :from';
        }

        if ($to !== null) {
            $dql .= ' AND e.detectedAt <= :to';
        }

        $dql .= ' ORDER BY e.detectedAt DESC';

        $query = $this->em->createQuery($dql)
            ->setParameter('scaleId', $scaleId)
            ->setParameter('productId', $productId)
            ->setParameter('type', ScaleEvent::TYPE_ANOMALIA);

        if ($from !== null) {
            $query->setParameter('from', $from);
        }

        if ($to !== null) {
            $query->setParameter('to', $to);
        }

        return $query->getResult();
    }
}
