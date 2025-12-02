<?php

namespace App\Report\Application\OutputPorts;

use App\Entity\Client\Report;
use App\Entity\Client\ReportExecution;

interface ReportExecutionRepositoryInterface
{
    public function findById(int $id): ?ReportExecution;

    /**
     * @return array<int, ReportExecution>
     */
    public function findByReport(Report $report): array;

    /**
     * Comprueba si un informe ya se ejecutó en un período determinado
     */
    public function wasExecutedInPeriod(Report $report, \DateTime $startDate, \DateTime $endDate): bool;

    public function save(ReportExecution $reportExecution): void;

    public function remove(ReportExecution $reportExecution): void;

    public function flush(): void;
}