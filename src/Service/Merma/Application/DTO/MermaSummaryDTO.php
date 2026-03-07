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
    ) {
    }

    public function getStatus(): string
    {
        if ($this->estimatedWastePct <= 3.0) {
            return 'excellent';
        }
        if ($this->estimatedWastePct <= 6.0) {
            return 'good';
        }
        if ($this->estimatedWastePct <= 8.0) {
            return 'warning';
        }
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
