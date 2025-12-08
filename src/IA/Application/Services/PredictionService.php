<?php

namespace App\IA\Application\Services;

use App\Entity\Client\Product;
use Psr\Log\LoggerInterface;

/**
 * Servicio de predicciones de consumo
 * Versión CORREGIDA para trabajar con entidades WeightsLog de Doctrine
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
            // Manejar tanto objetos como arrays
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
        $lastRecord = $weightsLog[count($weightsLog) - 1];

        // Extraer fecha (manejar tanto objetos como arrays)
        $firstDate = $this->extractDate($firstRecord);
        $lastDate = $this->extractDate($lastRecord);

        // Extraer peso actual
        $currentWeight = $this->extractRealWeight($lastRecord);
        $minStock = (float) $product->getStock();

        // Calcular días transcurridos
        $daysDifference = $lastDate->diff($firstDate)->days;
        if ($daysDifference == 0) {
            $daysDifference = 1; // Evitar división por cero
        }

        // CLAVE: Detectar SOLO consumos (ignora reposiciones)
        $consumptions = [];
        $restocks = [];

        for ($i = 1; $i < count($weightsLog); $i++) {
            $currentRecord = $weightsLog[$i];
            $previousRecord = $weightsLog[$i - 1];

            $currentRealWeight = $this->extractRealWeight($currentRecord);
            $previousRealWeight = $this->extractRealWeight($previousRecord);

            $weightDiff = $currentRealWeight - $previousRealWeight;

            // Reposición: aumento >1kg
            if ($weightDiff > 1.0) {
                $restocks[] = [
                    'amount' => $weightDiff,
                    'date' => $this->extractDate($currentRecord)
                ];
            }
            // Consumo: disminución >0.1kg
            elseif ($weightDiff < -0.1) {
                $consumptions[] = [
                    'amount' => abs($weightDiff),
                    'date' => $this->extractDate($currentRecord)
                ];
            }
        }

        // Calcular consumo total y diario
        if (empty($consumptions)) {
            // Sin consumos detectados, retornar valores seguros
            return [
                'product_id' => $product->getId(),
                'product_uuid' => $product->getUuid(),
                'product_name' => $product->getName(),
                'current_weight' => round($currentWeight, 2),
                'min_stock' => round($minStock, 2),
                'consumption_rate' => 0.0,
                'days_until_min_stock' => null,
                'stock_depletion_date' => null,
                'recommended_restock_date' => null,
                'days_serve_order' => $product->getDaysServeOrder() ?? 15,
                'alert_level' => 'unknown',
                'total_consumptions' => 0,
                'total_restocks' => count($restocks),
            ];
        }

        // Sumar todos los consumos
        $totalConsumption = array_sum(array_column($consumptions, 'amount'));

        // CORRECCIÓN: Dividir por días transcurridos, NO por número de registros
        $consumptionRate = $totalConsumption / $daysDifference;

        // Calcular días hasta llegar al stock mínimo
        $weightToMinStock = $currentWeight - $minStock;

        if ($consumptionRate > 0 && $weightToMinStock > 0) {
            $daysUntilMinStock = $weightToMinStock / $consumptionRate;
        } else {
            $daysUntilMinStock = null;
        }

        // Calcular fechas
        $now = new \DateTime();

        if ($daysUntilMinStock !== null) {
            $stockDepletionDate = clone $now;
            $stockDepletionDate->modify(sprintf('+%d days', ceil($daysUntilMinStock)));

            // Fecha recomendada de reposición (restar días de entrega)
            $daysServeOrder = $product->getDaysServeOrder() ?? 15;
            $recommendedRestockDate = clone $stockDepletionDate;
            $recommendedRestockDate->modify(sprintf('-%d days', $daysServeOrder));
        } else {
            $stockDepletionDate = null;
            $recommendedRestockDate = null;
        }

        // Determinar nivel de alerta
        $alertLevel = $this->calculateAlertLevel($daysUntilMinStock);

        return [
            'product_id' => $product->getId(),
            'product_uuid' => $product->getUuid(),
            'product_name' => $product->getName(),
            'current_weight' => round($currentWeight, 2),
            'min_stock' => round($minStock, 2),
            'consumption_rate' => round($consumptionRate, 6),
            'days_until_min_stock' => $daysUntilMinStock !== null ? round($daysUntilMinStock, 2) : null,
            'stock_depletion_date' => $stockDepletionDate ? $stockDepletionDate->format('Y-m-d H:i:s') : null,
            'recommended_restock_date' => $recommendedRestockDate ? $recommendedRestockDate->format('Y-m-d H:i:s') : null,
            'days_serve_order' => $product->getDaysServeOrder() ?? 15,
            'alert_level' => $alertLevel,
            'total_consumptions' => count($consumptions),
            'total_restocks' => count($restocks),
            'analysis_period_days' => $daysDifference,
        ];
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

        // Es un objeto - usar getter
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

        // Es un objeto - usar getter
        return (float) $record->getRealWeight();
    }

    /**
     * Calcula el nivel de alerta según días restantes
     *
     * @param float|null $daysUntilMinStock
     * @return string
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
     * Analiza patrones de consumo por hora y día
     *
     * @param array $weightsLog Array de entidades WeightsLog
     * @return array
     */
    public function analyzeConsumptionPatterns(array $weightsLog): array
    {
        if (count($weightsLog) < 10) {
            return [
                'status' => 'insufficient_data',
                'hourly_pattern' => [],
                'weekly_pattern' => [],
            ];
        }

        // Inicializar contadores
        $hourlyConsumption = array_fill(0, 24, 0);
        $hourlyCounts = array_fill(0, 24, 0);

        $weeklyConsumption = [
            'Monday' => 0, 'Tuesday' => 0, 'Wednesday' => 0, 'Thursday' => 0,
            'Friday' => 0, 'Saturday' => 0, 'Sunday' => 0
        ];
        $weeklyCounts = array_fill_keys(array_keys($weeklyConsumption), 0);

        // Analizar consumos
        for ($i = 1; $i < count($weightsLog); $i++) {
            $current = $weightsLog[$i];
            $previous = $weightsLog[$i - 1];

            $currentWeight = $this->extractRealWeight($current);
            $previousWeight = $this->extractRealWeight($previous);
            $consumption = $previousWeight - $currentWeight;

            // Solo consumos (no reposiciones)
            if ($consumption > 0.1) {
                $date = $this->extractDate($current);

                $hour = (int) $date->format('H');
                $dayOfWeek = $date->format('l');

                $hourlyConsumption[$hour] += $consumption;
                $hourlyCounts[$hour]++;

                $weeklyConsumption[$dayOfWeek] += $consumption;
                $weeklyCounts[$dayOfWeek]++;
            }
        }

        // Calcular promedios
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

        // Identificar horas pico
        arsort($hourlyAverage);
        $peakHours = array_slice(array_keys($hourlyAverage), 0, 3, true);

        // Identificar día más activo
        arsort($weeklyAverage);
        $peakDay = array_key_first($weeklyAverage);

        return [
            'status' => 'success',
            'hourly_pattern' => $hourlyAverage,
            'weekly_pattern' => $weeklyAverage,
            'peak_hours' => array_values($peakHours),
            'peak_day' => $peakDay,
        ];
    }
}