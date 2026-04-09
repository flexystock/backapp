<?php

namespace App\Service\Merma\Application\OutputPorts;

use App\Entity\Client\MermaConfig;
use App\Entity\Client\MermaMonthlyReport;
use App\Entity\Client\ScaleEvent;

/**
 * Lo que Merma necesita de la tabla scale_event.
 */
interface ScaleEventRepositoryInterface
{
    public function save(ScaleEvent $event): void;

    public function findById(int $id): ?ScaleEvent;

    /**
     * Suma los deltas de un tipo de evento en un rango de fechas.
     * Positivo para reposiciones, negativo para consumos/anomalías.
     */
    public function sumDeltaByType(
        int $scaleId,
        int $productId,
        string $type,
        \DateTimeInterface $from,
        \DateTimeInterface $to
    ): float;

    /**
     * Parejas (scaleId, productId) que tuvieron actividad en el mes.
     * @return array<array{scaleId: int, productId: int}>
     */
    public function findActiveScaleProductPairsForMonth(\DateTimeInterface $month): array;

    /**
     * Anomalías sin revisar de una balanza.
     * @return ScaleEvent[]
     */
    public function findPendingAnomalies(int $scaleId, int $productId, int $limit = 10): array;

    /** Cuenta anomalías pendientes de revisión */
    public function countPendingAnomalies(int $scaleId, int $productId): int;
}