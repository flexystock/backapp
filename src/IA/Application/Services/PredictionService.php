<?php

namespace App\IA\Application\Services;

use App\Entity\Client\Product;
use Psr\Log\LoggerInterface;

/**
 * Servicio de predicciones de consumo
 * Versión CORREGIDA para trabajar con entidades WeightsLog de Doctrine
 *
 * Fixes aplicados:
 * - Bug 1: consumptionRate ahora se calcula entre primer y último consumo detectado
 *          (no sobre todo el período incluyendo gaps y ciclos incompletos)
 * - Bug 2: recommendedRestockDate ahora tiene guard para cuando la fecha ya pasó
 *          (se devuelve "hoy" + flag restock_is_overdue = true)
 * - Mejora: isRestock() ahora usa umbral semántico basado en unidad del producto
 *           (mínimo configurado + 1 unidad) en lugar de un kg fijo
 */
class PredictionService
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Calcula la predicción de consumo para un producto
     *
     * @param array $weightsLog Array de entidades WeightsLog ordenados por fecha ASC
     * @param Product $product Producto
     * @return array Predicción con todos los datos
     */
    public function calculatePrediction(array $weightsLog, Product $product): array
    {
        // Validar que hay suficientes datos
        if (count($weightsLog) < 2) {
            throw new \InvalidArgumentException('Insufficient data for prediction (need at least 2 records)');
        }

        // Ordenar por fecha ASC (por si acaso)
        usort($weightsLog, function ($a, $b) {
            $dateA = is_array($a) ?
                ($a['date'] instanceof \DateTime ? $a['date'] : new \DateTime($a['date'])) :
                $a->getDate();
            $dateB = is_array($b) ?
                ($b['date'] instanceof \DateTime ? $b['date'] : new \DateTime($b['date'])) :
                $b->getDate();
            return $dateA <=> $dateB;
        });

        // Obtener datos básicos
        $firstRecord = $weightsLog[0];
        $lastRecord  = $weightsLog[count($weightsLog) - 1];

        $firstDate     = $this->extractDate($firstRecord);
        $lastDate      = $this->extractDate($lastRecord);
        $currentWeight = $this->extractRealWeight($lastRecord);
        $minStock      = (float) ($product->getMinimumStockInKg() ?? 0);

        // Días totales del período (fallback)
        $daysDifference = max(1, $lastDate->diff($firstDate)->days);

        // ── Detectar consumos y restocks ─────────────────────────────────────
        $consumptions = [];
        $restocks     = [];

        // Umbral de consumo: weight_range está en gramos → convertir a kg
        $consumptionThresholdKg = ($product->getWeightRange() !== null)
            ? (float) $product->getWeightRange() / 1000
            : 0.1; // fallback 100g

        for ($i = 1; $i < count($weightsLog); $i++) {
            $currentRealWeight  = $this->extractRealWeight($weightsLog[$i]);
            $previousRealWeight = $this->extractRealWeight($weightsLog[$i - 1]);
            $weightDiff         = $currentRealWeight - $previousRealWeight;

            if ($this->isRestock($previousRealWeight, $currentRealWeight, $product)) {
                // Restock semántico: sube y supera (mínimo + 1 unidad)
                $restocks[] = [
                    'amount' => $weightDiff,
                    'date'   => $this->extractDate($weightsLog[$i]),
                ];
            } elseif ($weightDiff < -$consumptionThresholdKg) {
                // Consumo real: baja más del umbral configurado en el producto
                $consumptions[] = [
                    'amount' => abs($weightDiff),
                    'date'   => $this->extractDate($weightsLog[$i]),
                ];
            }
        }

        // ── Sin consumos detectados → respuesta segura ───────────────────────
        $conversionFactor = $product->getConversionFactor() ?: 1.0;

        if (empty($consumptions)) {
            return [
                'product_id'               => $product->getId(),
                'product_uuid'             => $product->getUuid(),
                'product_name'             => $product->getName(),
                'current_weight'           => round($currentWeight, 2),
                'min_stock'                => round($minStock, 2),
                'consumption_rate'         => 0.0,
                'days_until_min_stock'     => null,
                'stock_depletion_date'     => null,
                'recommended_restock_date' => null,
                'restock_is_overdue'       => false,
                'days_serve_order'         => $product->getDaysServeOrder() ?? 15,
                'alert_level'              => 'unknown',
                'total_consumptions'       => 0,
                'total_restocks'           => count($restocks),
                'analysis_period_days'     => $daysDifference,
                'unit_name'                => $this->resolveUnitName($product),
                'current_stock_units'      => round($currentWeight / $conversionFactor, 2),
                'min_stock_units'          => round($minStock / $conversionFactor, 2),
                'consumption_rate_units'   => 0.0,
            ];
        }

        // ── Bug 1 FIX: consumptionRate sobre días entre consumos reales ──────
        // Usamos el span entre el primer y último consumo detectado,
        // evitando contaminar la tasa con gaps de restock o ciclos incompletos
        // al inicio del período analizado.
        $totalConsumption     = array_sum(array_column($consumptions, 'amount'));
        $firstConsumptionDate = $consumptions[0]['date'];
        $lastConsumptionDate  = end($consumptions)['date'];
        $consumptionDays      = max(1, $lastConsumptionDate->diff($firstConsumptionDate)->days);

        // Fallback al período total si solo hay un consumo (consumptionDays = 0)
        $consumptionRate = ($consumptionDays > 1)
            ? $totalConsumption / $consumptionDays
            : $totalConsumption / $daysDifference;

        // ── Calcular días hasta stock mínimo ─────────────────────────────────
        $weightToMinStock = $currentWeight - $minStock;

        if ($consumptionRate > 0 && $weightToMinStock > 0) {
            $daysUntilMinStock = $weightToMinStock / $consumptionRate;
        } else {
            $daysUntilMinStock = null;
        }

        // ── Calcular fechas ───────────────────────────────────────────────────
        $now             = new \DateTime();
        $restockIsOverdue = false;

        if ($daysUntilMinStock !== null) {
            $stockDepletionDate = clone $now;
            $stockDepletionDate->modify(sprintf('+%d days', ceil($daysUntilMinStock)));

            // getDaysServeOrder() devuelve int con default 0 (nunca null).
            // Si vale 0 significa que el usuario no ha configurado lead time:
            // en ese caso la fecha sugerida coincide con la de agotamiento.
            $daysServeOrder         = $product->getDaysServeOrder();
            $recommendedRestockDate = clone $stockDepletionDate;

            if ($daysServeOrder > 0) {
                $recommendedRestockDate->modify(sprintf('-%d days', $daysServeOrder));
            }

            // ── Bug 2 FIX: guard si la fecha de reposición ya pasó ───────────
            // Ocurre cuando el lead time (days_serve_order) es mayor que el
            // runway restante. En ese caso la reposición es urgente: pedir hoy.
            if ($recommendedRestockDate <= $now) {
                $recommendedRestockDate = clone $now;
                $restockIsOverdue       = true;
            }
        } else {
            $stockDepletionDate     = null;
            $recommendedRestockDate = null;
            $daysServeOrder         = $product->getDaysServeOrder();
        }

        // ── Nivel de alerta ───────────────────────────────────────────────────
        $alertLevel = $this->calculateAlertLevel($daysUntilMinStock);

        return [
            'product_id'               => $product->getId(),
            'product_uuid'             => $product->getUuid(),
            'product_name'             => $product->getName(),
            'current_weight'           => round($currentWeight, 2),
            'min_stock'                => round($minStock, 2),
            'consumption_rate'         => round($consumptionRate, 6),
            'days_until_min_stock'     => $daysUntilMinStock !== null ? round($daysUntilMinStock, 2) : null,
            'stock_depletion_date'     => $stockDepletionDate?->format('Y-m-d H:i:s'),
            'recommended_restock_date' => $recommendedRestockDate?->format('Y-m-d H:i:s'),
            'restock_is_overdue'       => $restockIsOverdue,   // true = pedir ya
            'days_serve_order'         => $daysServeOrder,
            'alert_level'              => $alertLevel,
            'total_consumptions'       => count($consumptions),
            'total_restocks'           => count($restocks),
            'analysis_period_days'     => $daysDifference,
            'unit_name'                => $this->resolveUnitName($product),
            'current_stock_units'      => round($currentWeight / $conversionFactor, 2),
            'min_stock_units'          => round($minStock / $conversionFactor, 2),
            'consumption_rate_units'   => round($consumptionRate / $conversionFactor, 4),
        ];
    }

    /**
     * Calcula el umbral de peso en kg que determina un restock real.
     *
     * Un restock es semánticamente válido cuando el peso resultante supera
     * (stock_mínimo + 1 unidad del producto), independientemente del tipo
     * de unidad configurada (briks, latas, botes, kg bruto, etc.)
     *
     * Usa getConversionFactor() de la entidad Product, que ya encapsula
     * la lógica de conversión según main_unit (0=kg, 1=unit1, 2=unit2).
     *
     * @param Product $product
     * @return float Umbral en kg
     */
    private function getRestockThresholdKg(Product $product): float
    {
        $minStockUnits = (float) ($product->getStock() ?? 0);
        $unitWeightKg  = $product->getConversionFactor(); // ya maneja main_unit internamente

        // (mínimo + 1 unidad) como umbral mínimo de una reposición real
        return ($minStockUnits + 1) * $unitWeightKg;
    }

    /**
     * Determina si el salto de peso entre dos lecturas es un restock real.
     *
     * Condiciones:
     *  1. El peso debe haber subido
     *  2. El peso resultante debe superar (mínimo + 1 unidad del producto)
     *
     * @param float $previousWeight Peso anterior en kg
     * @param float $currentWeight  Peso actual en kg
     * @param Product $product
     * @return bool
     */
    private function isRestock(float $previousWeight, float $currentWeight, Product $product): bool
    {
        if ($currentWeight <= $previousWeight) {
            return false;
        }

        return $currentWeight > $this->getRestockThresholdKg($product);
    }

    /**
     * Extrae la fecha de un registro (maneja objetos y arrays)
     *
     * @param mixed $record Entidad WeightsLog o array
     * @return \DateTime
     */
    private function extractDate($record): \DateTime
    {
        if (is_array($record)) {
            $date = $record['date'];
            return $date instanceof \DateTime ? $date : new \DateTime($date);
        }

        $date = $record->getDate();
        return $date instanceof \DateTime ? $date : new \DateTime($date);
    }

    /**
     * Extrae el peso real de un registro (maneja objetos y arrays)
     *
     * @param mixed $record Entidad WeightsLog o array
     * @return float
     */
    private function extractRealWeight($record): float
    {
        if (is_array($record)) {
            return (float) $record['real_weight'];
        }

        return (float) $record->getRealWeight();
    }

    /**
     * Calcula el nivel de alerta según días restantes hasta stock mínimo
     *
     * @param float|null $daysUntilMinStock
     * @return string critical | high | medium | low | unknown
     */
    private function calculateAlertLevel(?float $daysUntilMinStock): string
    {
        if ($daysUntilMinStock === null) {
            return 'unknown';
        }

        if ($daysUntilMinStock < 3) {
            return 'critical';
        } elseif ($daysUntilMinStock < 7) {
            return 'high';
        } elseif ($daysUntilMinStock < 14) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Devuelve el nombre legible de la unidad configurada para el producto.
     *
     * '1' → nombre de unidad 1 configurado (o 'Unidades' como fallback)
     * '2' → nombre de unidad 2 configurado (o 'Unidades' como fallback)
     * '0' o cualquier otro valor → 'kg'
     *
     * @param Product $product
     * @return string
     */
    private function resolveUnitName(Product $product): string
    {
        return match ($product->getMainUnit()) {
            '1' => $product->getNameUnit1() ?? 'Unidades',
            '2' => $product->getNameUnit2() ?? 'Unidades',
            default => 'kg',
        };
    }

    /**
     * Analiza patrones de consumo por hora y día de la semana
     *
     * @param array $weightsLog Array de entidades WeightsLog
     * @return array
     */
    public function analyzeConsumptionPatterns(array $weightsLog): array
    {
        if (count($weightsLog) < 10) {
            return [
                'status'         => 'insufficient_data',
                'hourly_pattern' => [],
                'weekly_pattern' => [],
            ];
        }

        $hourlyConsumption = array_fill(0, 24, 0);
        $hourlyCounts      = array_fill(0, 24, 0);

        $weeklyConsumption = [
            'Monday' => 0, 'Tuesday' => 0, 'Wednesday' => 0, 'Thursday' => 0,
            'Friday' => 0, 'Saturday' => 0, 'Sunday' => 0,
        ];
        $weeklyCounts = array_fill_keys(array_keys($weeklyConsumption), 0);

        for ($i = 1; $i < count($weightsLog); $i++) {
            $currentWeight  = $this->extractRealWeight($weightsLog[$i]);
            $previousWeight = $this->extractRealWeight($weightsLog[$i - 1]);
            $consumption    = $previousWeight - $currentWeight;

            if ($consumption > 0.1) {
                $date      = $this->extractDate($weightsLog[$i]);
                $hour      = (int) $date->format('H');
                $dayOfWeek = $date->format('l');

                $hourlyConsumption[$hour] += $consumption;
                $hourlyCounts[$hour]++;

                $weeklyConsumption[$dayOfWeek] += $consumption;
                $weeklyCounts[$dayOfWeek]++;
            }
        }

        $hourlyAverage = [];
        for ($h = 0; $h < 24; $h++) {
            $hourlyAverage[$h] = $hourlyCounts[$h] > 0
                ? round($hourlyConsumption[$h] / $hourlyCounts[$h], 3)
                : 0;
        }

        $weeklyAverage = [];
        foreach ($weeklyConsumption as $day => $total) {
            $weeklyAverage[$day] = $weeklyCounts[$day] > 0
                ? round($total / $weeklyCounts[$day], 3)
                : 0;
        }

        arsort($hourlyAverage);
        $peakHours = array_slice(array_keys($hourlyAverage), 0, 3, true);

        arsort($weeklyAverage);
        $peakDay = array_key_first($weeklyAverage);

        return [
            'status'         => 'success',
            'hourly_pattern' => $hourlyAverage,
            'weekly_pattern' => $weeklyAverage,
            'peak_hours'     => array_values($peakHours),
            'peak_day'       => $peakDay,
        ];
    }
}