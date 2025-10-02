<?php

namespace App\Dashboard\Application\UseCases;

use App\Dashboard\Application\DTO\GetDashboardSummaryRequest;
use App\Dashboard\Application\DTO\GetDashboardSummaryResponse;
use App\Dashboard\Application\InputPorts\GetDashboardSummaryUseCaseInterface;
use App\Product\Application\DTO\GetInfoToDashboardMainRequest;
use App\Product\Application\InputPorts\GetInfoToDashboardMainUseCaseInterface;
use App\Scales\Application\DTO\GetInfoScalesToDashboardMainRequest;
use App\Scales\Application\InputPorts\GetInfoScalesToDashboardMainUseCaseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class GetDashboardSummaryUseCase implements GetDashboardSummaryUseCaseInterface
{
    private const LOW_BATTERY_THRESHOLD = 20;

    public function __construct(
        private readonly GetInfoToDashboardMainUseCaseInterface $getInfoToDashboardMainUseCase,
        private readonly GetInfoScalesToDashboardMainUseCaseInterface $getInfoScalesToDashboardMainUseCase,
        private readonly LoggerInterface $logger
    ) {
    }

    public function execute(GetDashboardSummaryRequest $request): GetDashboardSummaryResponse
    {
        $uuidClient = $request->getUuidClient();

        try {
            $productResponse = $this->getInfoToDashboardMainUseCase->execute(
                new GetInfoToDashboardMainRequest($uuidClient)
            );

            if (200 !== $productResponse->getStatusCode()) {
                return new GetDashboardSummaryResponse(null, null, $productResponse->getError(), $productResponse->getStatusCode());
            }

            $scalesResponse = $this->getInfoScalesToDashboardMainUseCase->execute(
                new GetInfoScalesToDashboardMainRequest($uuidClient)
            );

            if (200 !== $scalesResponse->getStatusCode()) {
                return new GetDashboardSummaryResponse(null, null, $scalesResponse->getError(), $scalesResponse->getStatusCode());
            }

            $lowStockProducts = $this->extractLowStockProducts($productResponse->getProduct() ?? []);
            $lowBatteryScales = $this->extractLowBatteryScales($scalesResponse->getScale() ?? []);

            return new GetDashboardSummaryResponse($lowStockProducts, $lowBatteryScales, null, 200);
        } catch (Throwable $exception) {
            $this->logger->error('GetDashboardSummaryUseCase: unexpected error while aggregating dashboard data', [
                'uuid_client' => $uuidClient,
                'exception' => $exception,
            ]);

            return new GetDashboardSummaryResponse(null, null, 'Internal Server Error', 500);
        }
    }

    /**
     * @param array<int, mixed> $products
     *
     * @return array<int, array<string, mixed>>
     */
    private function extractLowStockProducts(array $products): array
    {
        return array_values(array_filter($products, static function ($product): bool {
            if (!is_array($product)) {
                return false;
            }

            $minimumStock = $product['stock'] ?? null;
            $currentWeight = $product['real_weight_sum_kg'] ?? null;

            if (!is_numeric($minimumStock) || !is_numeric($currentWeight)) {
                return false;
            }

            return (float) $currentWeight <= (float) $minimumStock;
        }));
    }

    /**
     * @param array<string, mixed> $scalesData
     *
     * @return array<int, array<string, mixed>>
     */
    private function extractLowBatteryScales(array $scalesData): array
    {
        $assignedScales = $scalesData['assignedScales'] ?? [];

        if (!is_array($assignedScales)) {
            return [];
        }

        return array_values(array_filter($assignedScales, static function ($scale): bool {
            if (!is_array($scale) || !array_key_exists('voltage_percentage', $scale)) {
                return false;
            }

            $voltage = $scale['voltage_percentage'];

            if (!is_numeric($voltage)) {
                return false;
            }

            return (float) $voltage <= self::LOW_BATTERY_THRESHOLD;
        }));
    }
}
