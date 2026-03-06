<?php

namespace App\Service\Merma\Application\InputPorts;

use App\Service\Merma\Application\DTO\MermaReportDTO;
use App\Service\Merma\Application\DTO\MermaSummaryDTO;
use App\Service\Merma\Application\DTO\ScaleEventResultDTO;
use App\Service\Merma\Application\DTO\ScaleReadingDTO;

/**
 * Contrato para las operaciones del dashboard de merma.
 * Llamado desde el MermaEventController (InputAdapter web).
 */
interface MermaDashboardInterface
{
    /**
     * Resumen en tiempo real del mes actual para el widget del dashboard.
     */
    public function getSummary(int $scaleId, int $productId): MermaSummaryDTO;

    /**
     * Confirma una anomalía como sustracción real.
     */
    public function confirmAnomaly(int $eventId): void;

    /**
     * Descarta una anomalía (fue un accidente, error operativo, etc.).
     */
    public function discardAnomaly(int $eventId): void;

    /**
     * Historial de informes mensuales de una balanza.
     * @return MermaReportDTO[]
     */
    public function getMonthlyHistory(int $scaleId, int $productId, int $limit = 12): array;
}