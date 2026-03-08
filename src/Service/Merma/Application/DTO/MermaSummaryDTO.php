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
}
