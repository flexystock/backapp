<?php

namespace App\Service\Merma;

use App\Entity\Client\MermaConfig;

/**
 * ScaleEventDetectorService
 *
 * Servicio de dominio puro — sin dependencias de framework ni de base de datos.
 * Toda la lógica de clasificación de eventos vive aquí.
 * El UseCase orquesta (persiste, notifica); este servicio solo razona.
 */
final class ScaleEventDetectorService
{
    /**
     * Clasifica un cambio de peso en un tipo de evento.
     *
     * @return ScaleEventClassification|null  Null si el delta está por debajo del umbral (ruido)
     */
    public function classify(
        float              $previousWeight,
        float              $newWeight,
        \DateTimeInterface $readAt,
        MermaConfig        $config,
        ?float             $pricePerKg = null,
    ): ?ScaleEventClassification {

        $delta = round($newWeight - $previousWeight, 3);

        // Filtrar ruido del sensor
        if (abs($delta) < $config->getAnomalyThresholdKg()) {
            return null;
        }

        $type = $this->classifyType($delta, $readAt, $config);

        $deltaCost = ($pricePerKg !== null && $pricePerKg > 0)
            ? round(abs($delta) * $pricePerKg, 2)
            : null;

        return new ScaleEventClassification(
            type:      $type,
            deltaKg:   $delta,
            deltaCost: $deltaCost,
        );
    }

    private function classifyType(float $delta, \DateTimeInterface $readAt, MermaConfig $config): string
    {
        // Subida de peso → siempre es reposición
        if ($delta > 0) {
            return 'reposicion';
        }

        // Bajada dentro del horario de servicio → consumo normal
        if ($config->isDuringService($readAt)) {
            return 'consumo';
        }

        // Bajada fuera del horario → anomalía (posible sustracción)
        return 'anomalia';
    }
}