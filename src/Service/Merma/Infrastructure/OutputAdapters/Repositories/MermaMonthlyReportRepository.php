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
// MermaMonthlyReportRepository
// ═══════════════════════════════════════════════════════

final class MermaMonthlyReportRepository
    extends ServiceEntityRepository
    implements MermaMonthlyReportRepositoryInterface
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct($registry, MermaMonthlyReport::class);
    }

    public function save(MermaMonthlyReport $report): void
    {
        $this->em->persist($report);
        $this->em->flush();
    }

    public function findForPeriod(int $scaleId, int $productId, \DateTimeInterface $month): ?MermaMonthlyReport
    {
        return $this->findOneBy([
            'scaleId'     => $scaleId,
            'productId'   => $productId,
            'periodMonth' => $month,
        ]);
    }

    public function findHistoryForScale(int $scaleId, int $productId, int $limit = 12): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.scaleId = :scaleId')
            ->andWhere('r.productId = :productId')
            ->setParameter('scaleId',   $scaleId)
            ->setParameter('productId', $productId)
            ->orderBy('r.periodMonth', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}