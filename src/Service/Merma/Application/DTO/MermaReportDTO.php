<?php

namespace App\Service\Merma\Application\DTO;

/**
 * DTO de salida del generador de informes mensuales.
 */
final class MermaReportDTO
{
    public function __construct(
        public readonly int    $reportId,
        public readonly int    $productId,
        public readonly int    $scaleId,
        public readonly string $periodLabel,   // "Marzo 2026"
        public readonly float  $inputKg,
        public readonly float  $consumedKg,
        public readonly float  $anomalyKg,
        public readonly float  $actualWasteKg,
        public readonly float  $wastePct,
        public readonly float  $wasteCostEuros,
        public readonly float  $savedVsBaseline,
        public readonly string $status,        // 'excellent' | 'good' | 'warning' | 'critical'
    ) {}
}