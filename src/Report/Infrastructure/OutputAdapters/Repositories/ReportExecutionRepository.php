<?php

namespace App\Report\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\Report;
use App\Entity\Client\ReportExecution;
use App\Report\Application\OutputPorts\ReportExecutionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class ReportExecutionRepository implements ReportExecutionRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function findById(int $id): ?ReportExecution
    {
        return $this->entityManager->getRepository(ReportExecution::class)->find($id);
    }

    /**
     * @return array<int, ReportExecution>
     */
    public function findByReport(Report $report): array
    {
        return $this->entityManager->getRepository(ReportExecution::class)
            ->findBy(['report' => $report], ['executedAt' => 'DESC']);
    }

    public function wasExecutedInPeriod(Report $report, \DateTime $startDate, \DateTime $endDate): bool
    {
        $qb = $this->entityManager->createQueryBuilder();

        $count = $qb->select('COUNT(re.id)')
            ->from(ReportExecution::class, 're')
            ->where('re.report = :report')
            ->andWhere('re.executedAt >= :startDate')
            ->andWhere('re.executedAt <= :endDate')
            ->andWhere('re.status = :status')
            ->setParameter('report', $report)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('status', 'success')
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $count > 0;
    }

    public function save(ReportExecution $reportExecution): void
    {
        $this->entityManager->persist($reportExecution);
    }

    public function remove(ReportExecution $reportExecution): void
    {
        $this->entityManager->remove($reportExecution);
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}