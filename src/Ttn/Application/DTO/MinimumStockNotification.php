<?php

namespace App\Ttn\Application\DTO;

class MinimumStockNotification
{
    public function __construct(
        private readonly string $uuidClient,
        private readonly string $clientName,
        private readonly array $recipientEmails,
        private readonly int $productId,
        private readonly string $productName,
        private readonly int $scaleId,
        private readonly string $deviceId,
        private readonly float $currentWeight,
        private readonly float $minimumStock,
        private readonly float $weightRange,
        private readonly string $nameUnit
    ) {
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getClientName(): string
    {
        return $this->clientName;
    }

    /**
     * @return string[]
     */
    public function getRecipientEmails(): array
    {
        return $this->recipientEmails;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getScaleId(): int
    {
        return $this->scaleId;
    }

    public function getDeviceId(): string
    {
        return $this->deviceId;
    }

    public function getCurrentWeight(): float
    {
        return $this->currentWeight;
    }

    public function getMinimumStock(): float
    {
        return $this->minimumStock;
    }

    public function getWeightRange(): float
    {
        return $this->weightRange;
    }

    public function getNameUnit(): string
    {
        return $this->nameUnit;
    }
}
