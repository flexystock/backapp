<?php

namespace App\Service\Merma\Application\DTO;

/**
 * DTO de salida del detector — lo que devuelve el UseCase al InputAdapter.
 * Null si la lectura no generó ningún evento (ruido de sensor, primera lectura, etc.)
 */
final class ScaleEventResultDTO
{
    public function __construct(
        public readonly int    $eventId,
        public readonly string $type,          // 'reposicion' | 'consumo' | 'anomalia'
        public readonly float  $weightBefore,
        public readonly float  $weightAfter,
        public readonly float  $deltaKg,
        public readonly ?float $deltaCost,
        public readonly \DateTimeInterface $detectedAt,
    ) {}

    public function isAnomalia(): bool
    {
        return $this->type === 'anomalia';
    }
}