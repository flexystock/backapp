<?php

namespace App\Report\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\Report;
use App\Report\Application\OutputPorts\ReportRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class ReportRepository implements ReportRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function findById(int $id): ?Report
    {
        return $this->entityManager->getRepository(Report::class)->find($id);
    }

    /**
     * @return array<int, Report>
     */
    public function findAll(): array
    {
        return $this->entityManager->getRepository(Report::class)
            ->findBy([], ['id' => 'ASC']);
    }

    public function save(Report $report): void
    {
        $this->entityManager->persist($report);
    }

    public function remove(Report $report): void
    {
        $this->entityManager->remove($report);
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }

    public function count(): int
    {
        return $this->entityManager->getRepository(Report::class)->count([]);
    }
}
