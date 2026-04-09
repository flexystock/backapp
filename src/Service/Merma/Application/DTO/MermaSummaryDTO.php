<?php

namespace App\Service\Merma\Application\DTO;

/**
 * DTO para el widget del dashboard (datos en tiempo real, sin informe mensual).
 */
final class MermaSummaryDTO
{
    public function __construct(
        public readonly float  $inputKg,
        public readonly float  $consumedKg,
        public readonly float  $anomalyKg,
        public readonly float  $estimatedWasteKg,
        public readonly float  $estimatedWastePct,
        public readonly float  $estimatedCostEuros,
        public readonly int    $pendingAnomaliesCount,
        public readonly float  $prevMonthWastePct,
        public readonly float  $prevMonthCostEuros,
        public readonly int    $rendimientoEsperadoPct,
        public readonly float  $anomalyCostEuros = 0.0,
        private readonly float $currentStockKg = 0.0,
        private readonly string $unitLabel = 'kg',
        private readonly float  $conversionFactor = 1.0,
    ) {
    }

    public function getStatus(): string
    {
        $maxWastePct = 100 - $this->rendimientoEsperadoPct; // ej: 100-80 = 20%

        if ($this->estimatedWastePct <= $maxWastePct * 0.25) {
            return 'excellent';
        } // < 5%
        if ($this->estimatedWastePct <= $maxWastePct * 0.50) {
            return 'good';
        }      // < 10%
        if ($this->estimatedWastePct <= $maxWastePct * 0.75) {
            return 'warning';
        }   // < 15%
        return 'critical';
    }

    public function getTrend(): string
    {
        if ($this->prevMonthWastePct <= 0) {
            return 'neutral';
        }
        if ($this->estimatedWastePct < $this->prevMonthWastePct) {
            return 'improving';
        }
        if ($this->estimatedWastePct > $this->prevMonthWastePct) {
            return 'worsening';
        }
        return 'neutral';
    }

    public function getAnomalyCostEuros(): float { return $this->anomalyCostEuros; }

    public function getCurrentStockKg(): float
    {
        return $this->currentStockKg;
    }
    public function getUnitLabel(): string
    {
        return $this->unitLabel;
    }
    public function getConversionFactor(): float
    {
        return $this->conversionFactor;
    }
}
