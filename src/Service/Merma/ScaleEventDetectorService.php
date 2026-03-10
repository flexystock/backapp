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
        array              $businessHours = [],
        float              $thresholdKg,
        array              $productServiceHours = [],
    ): ?ScaleEventClassification {

        $delta = round($newWeight - $previousWeight, 3);

        if (abs($delta) < $thresholdKg) {
            return null;
        }

        $type = $this->classifyType($delta, $readAt, $businessHours, $productServiceHours);

        $deltaCost = ($pricePerKg !== null && $pricePerKg > 0)
            ? round(abs($delta) * $pricePerKg, 2)
            : null;

        return new ScaleEventClassification(
            type:      $type,
            deltaKg:   $delta,
            deltaCost: $deltaCost,
        );
    }

    private function classifyType(
        float              $delta,
        \DateTimeInterface $readAt,
        array              $businessHours,
        array              $productServiceHours = [],
    ): string {
        // Subida de peso → siempre es reposición
        if ($delta > 0) {
            return 'reposicion';
        }

        // Bajada — determinar si es dentro o fuera del horario de servicio
        $dayOfWeek = (int) $readAt->format('N'); // 1=lunes ... 7=domingo

        if (!empty($productServiceHours)) {
            $inServiceHour = false;
            foreach ($productServiceHours as $psh) {
                if ($psh->getDayOfWeek() === $dayOfWeek && $psh->coversDateTime($readAt)) {
                    $inServiceHour = true;
                    break;
                }
            }

            return $inServiceHour ? 'consumo' : 'anomalia';
        }

        $todayHours = array_filter(
            $businessHours,
            fn(\App\Entity\Client\BusinessHour $bh) => $bh->getDayOfWeek() === $dayOfWeek
        );

        $isDuringService = array_reduce(
            $todayHours,
            fn(bool $carry, \App\Entity\Client\BusinessHour $bh) => $carry || $bh->coversDateTime($readAt),
            false
        );

        return $isDuringService ? 'consumo' : 'anomalia';
    }
}