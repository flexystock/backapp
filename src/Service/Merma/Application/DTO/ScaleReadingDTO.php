<?php

namespace App\Service\Merma\Application\DTO;

/**
 * DTO de entrada para el detector de eventos.
 * Se construye desde el InputAdapter (webhook TTN) antes de llamar al UseCase.
 */
final class ScaleReadingDTO
{
    public function __construct(
        public readonly int    $scaleId,
        public readonly int    $productId,
        public readonly float  $weightKg,
        public readonly \DateTimeInterface $readAt,
    ) {}
}