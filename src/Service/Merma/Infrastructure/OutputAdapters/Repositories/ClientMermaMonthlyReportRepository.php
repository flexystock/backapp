<?php

namespace App\Service\Merma\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\MermaMonthlyReport;
use App\Service\Merma\Application\OutputPorts\MermaMonthlyReportRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Implementación real del repositorio de informes mensuales de merma.
 * Se instancia con el EntityManager del cliente (multi-tenant).
 */
final class ClientMermaMonthlyReportRepository implements MermaMonthlyReportRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function save(MermaMonthlyReport $report): void
    {
        $this->em->persist($report);
        $this->em->flush();
    }

    public function findForPeriod(int $scaleId, int $productId, \DateTimeInterface $month): ?MermaMonthlyReport
    {
        $monthStart = new \DateTime($month->format('Y-m-01') . ' 00:00:00');

        return $this->em->createQuery(
            'SELECT r
             FROM App\Entity\Client\MermaMonthlyReport r
             WHERE IDENTITY(r.scale) = :scaleId
               AND IDENTITY(r.product) = :productId
               AND r.periodMonth = :month'
        )
            ->setParameter('scaleId', $scaleId)
            ->setParameter('productId', $productId)
            ->setParameter('month', $monthStart)
            ->getOneOrNullResult();
    }

    public function findHistoryForScale(int $scaleId, int $productId, int $limit = 12): array
    {
        return $this->em->createQuery(
            'SELECT r
             FROM App\Entity\Client\MermaMonthlyReport r
             WHERE IDENTITY(r.scale) = :scaleId
               AND IDENTITY(r.product) = :productId
             ORDER BY r.periodMonth DESC'
        )
            ->setParameter('scaleId', $scaleId)
            ->setParameter('productId', $productId)
            ->setMaxResults($limit)
            ->getResult();
    }
}
