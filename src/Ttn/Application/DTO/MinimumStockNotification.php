<?php

namespace App\Ttn\Application\DTO;

class MinimumStockNotification
{
    public function __construct(
        private readonly string $uuidClient,
        private readonly string $clientName,
        private readonly ?string $recipientEmail,
        private readonly int $productId,
        private readonly string $productName,
        private readonly int $scaleId,
        private readonly string $deviceId,
        private readonly float $currentWeight,
        private readonly float $minimumStock,
        private readonly float $weightRange
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

    public function getRecipientEmail(): ?string
    {
        return $this->recipientEmail;
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
}
