<?php

namespace App\Service\Merma\Infrastructure\OutputAdapters\Repositories;

use App\Entity\Client\MermaMonthlyReport;
use App\Service\Merma\Application\OutputPorts\MermaMonthlyReportRepositoryInterface;

/**
 * Stub — la lógica real usa el EntityManager del cliente directamente.
 */
final class MermaMonthlyReportRepository implements MermaMonthlyReportRepositoryInterface
{
    public function save(MermaMonthlyReport $report): void {}

    public function findForPeriod(int $scaleId, int $productId, \DateTimeInterface $month): ?MermaMonthlyReport
    {
        return null;
    }

    public function findHistoryForScale(int $scaleId, int $productId, int $limit = 12): array
    {
        return [];
    }
}
