<?php

namespace App\Service\Merma;

/**
 * Value Object inmutable con el resultado de la clasificación.
 * Lo devuelve ScaleEventDetectorService, lo consume el UseCase.
 */
final class ScaleEventClassification
{
    public function __construct(
        public readonly string $type,       // 'reposicion' | 'consumo' | 'anomalia'
        public readonly float  $deltaKg,    // positivo=reposición, negativo=consumo/anomalía
        public readonly ?float $deltaCost,  // euros, null si el producto no tiene precio
    ) {}
}