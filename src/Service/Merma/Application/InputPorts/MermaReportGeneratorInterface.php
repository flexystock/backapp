<?php

namespace App\Service\Merma\Application\InputPorts;

use App\Service\Merma\Application\DTO\MermaReportDTO;
use App\Service\Merma\Application\DTO\MermaSummaryDTO;
use App\Service\Merma\Application\DTO\ScaleEventResultDTO;
use App\Service\Merma\Application\DTO\ScaleReadingDTO;


/**
 * Contrato para el generador de informes mensuales.
 * Llamado desde el comando de consola (cron).
 */
interface MermaReportGeneratorInterface
{
    /**
     * Genera los informes del mes indicado (o mes anterior si null) para todas las balanzas.
     * Retorna el número de informes generados.
     */
    public function generateForAllScales(?\DateTimeInterface $month = null): int;

    /**
     * Genera el informe de un mes para una balanza+producto concretos.
     * Útil para regenerar un informe específico o para tests.
     */
    public function generateForScale(int $scaleId, int $productId, \DateTimeInterface $month): ?MermaReportDTO;
}
