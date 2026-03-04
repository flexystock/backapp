<?php

namespace App\Service\Merma;

use App\Entity\Client\ScaleEvent;
use App\Service\Merma\Application\OutputPorts\MermaConfigRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\ScaleEventRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\ScaleReadingRepositoryInterface;

/**
 * MermaReportGeneratorService
 *
 * Servicio de dominio puro — encapsula el cálculo de merma mensual.
 * Sin persistencia, sin emails: solo recibe datos y devuelve el resultado calculado.
 * El UseCase es responsable de persistir y notificar.
 *
 * Fórmula central:
 *   merma_real = stock_inicio + input_mes - consumo_en_horario - stock_fin
 */
final class MermaReportGeneratorService
{
    /** Merma media del sector hostelería — benchmark para calcular el ahorro */
    private const SECTOR_BASELINE_PCT = 8.0;

    /**
     * Calcula todos los datos del informe mensual para una (balanza, producto).
     * Retorna null si no hubo actividad ese mes.
     */
    public function calculate(
        int                              $scaleId,
        int                              $productId,
        \DateTimeInterface               $monthStart,
        \DateTimeInterface               $monthEnd,
        ScaleEventRepositoryInterface    $eventRepo,
        ScaleReadingRepositoryInterface  $readingRepo,
        MermaConfigRepositoryInterface   $configRepo,
    ): ?MermaCalculationResult {

        // ── Datos del mes ─────────────────────────────────────
        $inputKg    = $eventRepo->sumDeltaByType($scaleId, $productId, ScaleEvent::TYPE_REPOSICION, $monthStart, $monthEnd);
        $consumedKg = abs($eventRepo->sumDeltaByType($scaleId, $productId, ScaleEvent::TYPE_CONSUMO, $monthStart, $monthEnd));
        $anomalyKg  = abs($eventRepo->sumDeltaByType($scaleId, $productId, ScaleEvent::TYPE_ANOMALIA, $monthStart, $monthEnd));

        // Sin actividad → no generar informe vacío
        if ($inputKg <= 0 && $consumedKg <= 0) {
            return null;
        }

        // ── Stocks inicio y fin de mes ────────────────────────
        $stockStartKg = $readingRepo->findWeightAt($scaleId, $monthStart) ?? 0.0;
        $stockEndKg   = $readingRepo->findWeightAt($scaleId, $monthEnd)   ?? 0.0;

        // ── Cálculo de merma real ─────────────────────────────
        // Todo lo que entró + lo que había - lo que se consumió en servicio - lo que queda
        $actualWasteKg = max(0.0, round(
            $stockStartKg + $inputKg - $consumedKg - $stockEndKg,
            3
        ));

        $wastePct = $inputKg > 0
            ? round(($actualWasteKg / $inputKg) * 100, 2)
            : 0.0;

        // ── Merma operativa esperada (según configuración del producto) ──
        $config          = $configRepo->findByProductId($productId);
        $expectedWasteKg = $config
            ? $config->expectedWasteKg($inputKg)
            : round($inputKg * 0.20, 3); // fallback: 20% si no hay config

        // ── Ahorro vs. baseline del sector ────────────────────
        // "Sin FlexyStock tendrías un 8% de merma. Con FlexyStock tienes X%. La diferencia en euros es el ahorro."
        $baselineWasteKg = $inputKg * (self::SECTOR_BASELINE_PCT / 100);
        $savedKg         = max(0.0, $baselineWasteKg - $actualWasteKg);

        return new MermaCalculationResult(
            inputKg:         $inputKg,
            consumedKg:      $consumedKg,
            anomalyKg:       $anomalyKg,
            stockStartKg:    $stockStartKg,
            stockEndKg:      $stockEndKg,
            expectedWasteKg: $expectedWasteKg,
            actualWasteKg:   $actualWasteKg,
            wastePct:        $wastePct,
            savedKg:         $savedKg,
        );
    }

    /**
     * Calcula waste_cost y saved_vs_baseline a partir del resultado y el precio del producto.
     * Separado para que el UseCase pueda enriquecer el resultado cuando tenga acceso al precio.
     */
    public function applyPricing(MermaCalculationResult $result, float $pricePerKg): MermaCalculationResult
    {
        return new MermaCalculationResult(
            inputKg:          $result->inputKg,
            consumedKg:       $result->consumedKg,
            anomalyKg:        $result->anomalyKg,
            stockStartKg:     $result->stockStartKg,
            stockEndKg:       $result->stockEndKg,
            expectedWasteKg:  $result->expectedWasteKg,
            actualWasteKg:    $result->actualWasteKg,
            wastePct:         $result->wastePct,
            savedKg:          $result->savedKg,
            wasteCostEuros:   round($result->actualWasteKg * $pricePerKg, 2),
            savedVsBaseline:  round($result->savedKg * $pricePerKg, 2),
        );
    }
}