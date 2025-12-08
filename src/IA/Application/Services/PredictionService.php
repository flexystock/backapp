<?php

namespace App\IA\Application\Services;

class PredictionService
{
    private const SECONDS_PER_DAY = 86400;

    /**
     * Calculate consumption prediction for a product based on weight logs.
     */
    public function calculatePrediction(array $weightsLog, $product): array
    {
        // Prepare data for linear regression
        $dataPoints = [];
        $firstTimestamp = null;

        foreach ($weightsLog as $log) {
            $timestamp = $log->getDate()->getTimestamp();
            if ($firstTimestamp === null) {
                $firstTimestamp = $timestamp;
            }
            // Convert timestamp to days from start
            $daysSinceStart = ($timestamp - $firstTimestamp) / self::SECONDS_PER_DAY;
            $dataPoints[] = [
                'x' => $daysSinceStart,
                'y' => (float) $log->getAdjustWeight(),
            ];
        }

        if (count($dataPoints) < 2) {
            throw new \RuntimeException('INSUFFICIENT_DATA');
        }

        // Calculate linear regression (y = mx + b)
        $regression = $this->linearRegression($dataPoints);
        $slope = $regression['slope'];
        $intercept = $regression['intercept'];

        // Get current stock and minimum stock
        $currentWeight = end($dataPoints)['y'];
        $minStock = $product->getStock() ?? 0;

        // Calculate days until stock depletes to minimum
        $daysUntilMinStock = null;
        $stockDepletionDate = null;
        $recommendedRestockDate = null;

        if ($slope < 0) {
            // Product is being consumed (negative slope)
            $daysUntilMinStock = ($minStock - $currentWeight) / $slope;

            if ($daysUntilMinStock > 0) {
                $stockDepletionDate = new \DateTime();
                $stockDepletionDate->modify('+'.round($daysUntilMinStock).' days');

                // Recommend restocking based on days_serve_order
                $daysServeOrder = $product->getDaysServeOrder();
                $recommendedRestockDate = clone $stockDepletionDate;
                $recommendedRestockDate->modify('-'.$daysServeOrder.' days');
            }
        }

        return [
            'product_id' => $product->getId(),
            'product_uuid' => $product->getUuid(),
            'product_name' => $product->getName(),
            'current_weight' => $currentWeight,
            'min_stock' => $minStock,
            'consumption_rate' => abs($slope), // kg/day
            'days_until_min_stock' => $daysUntilMinStock ? round($daysUntilMinStock, 2) : null,
            'stock_depletion_date' => $stockDepletionDate ? $stockDepletionDate->format('Y-m-d H:i:s') : null,
            'recommended_restock_date' => $recommendedRestockDate ? $recommendedRestockDate->format('Y-m-d H:i:s') : null,
            'days_serve_order' => $product->getDaysServeOrder(),
        ];
    }

    /**
     * Calculate linear regression coefficients (slope and intercept).
     *
     * @throws \RuntimeException if regression cannot be calculated (e.g., division by zero)
     */
    private function linearRegression(array $dataPoints): array
    {
        $n = count($dataPoints);
        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        foreach ($dataPoints as $point) {
            $sumX += $point['x'];
            $sumY += $point['y'];
            $sumXY += $point['x'] * $point['y'];
            $sumX2 += $point['x'] * $point['x'];
        }

        $denominator = ($n * $sumX2 - $sumX * $sumX);

        // Check for division by zero (all x values are identical)
        if (abs($denominator) < 0.0001) {
            throw new \RuntimeException('INSUFFICIENT_DATA');
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / $denominator;
        $intercept = ($sumY - $slope * $sumX) / $n;

        return [
            'slope' => $slope,
            'intercept' => $intercept,
        ];
    }
}
