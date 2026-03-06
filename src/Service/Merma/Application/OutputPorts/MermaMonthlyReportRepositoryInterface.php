<?php

namespace App\Service\Merma\Application\OutputPorts;

use App\Entity\Client\MermaConfig;
use App\Entity\Client\MermaMonthlyReport;
use App\Entity\Client\ScaleEvent;

/**
 * Lo que Merma necesita de los informes mensuales.
 */
interface MermaMonthlyReportRepositoryInterface
{
    public function save(MermaMonthlyReport $report): void;

    /**
     * Busca el informe de un mes concreto para una (balanza, producto).
     * Retorna null si no existe todavía.
     */
    public function findForPeriod(int $scaleId, int $productId, \DateTimeInterface $month): ?MermaMonthlyReport;

    /**
     * Historial de informes ordenado por mes descendente.
     * @return MermaMonthlyReport[]
     */
    public function findHistoryForScale(int $scaleId, int $productId, int $limit = 12): array;
}
