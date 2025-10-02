<?php

namespace App\Dashboard\Application\DTO;

class GetDashboardSummaryResponse
{
    private ?array $lowStockProducts;
    private ?array $lowBatteryScales;
    private ?string $error;
    private int $statusCode;

    public function __construct(?array $lowStockProducts, ?array $lowBatteryScales, ?string $error, int $statusCode)
    {
        $this->lowStockProducts = $lowStockProducts;
        $this->lowBatteryScales = $lowBatteryScales;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getLowStockProducts(): ?array
    {
        return $this->lowStockProducts;
    }

    public function getLowBatteryScales(): ?array
    {
        return $this->lowBatteryScales;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
