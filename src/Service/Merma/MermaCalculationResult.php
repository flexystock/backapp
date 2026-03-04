<?php

namespace App\Service\Merma;

use App\Entity\Client\ScaleEvent;
use App\Service\Merma\Application\OutputPorts\MermaConfigRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\ScaleEventRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\ScaleReadingRepositoryInterface;

/**
 * Value Object inmutable con el resultado del cálculo mensual.
 * Lo devuelve MermaReportGeneratorService, lo consume el UseCase.
 */
final class MermaCalculationResult
{
    public function __construct(
        public readonly float $inputKg,
        public readonly float $consumedKg,
        public readonly float $anomalyKg,
        public readonly float $stockStartKg,
        public readonly float $stockEndKg,
        public readonly float $expectedWasteKg,
        public readonly float $actualWasteKg,
        public readonly float $wastePct,
        public readonly float $savedKg,
        public readonly float $wasteCostEuros  = 0.0,
        public readonly float $savedVsBaseline = 0.0,
    ) {}
}