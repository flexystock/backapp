<?php

namespace App\Ttn\Application\DTO;

use DateTimeImmutable;

class WeightVariationAlertNotification
{
    public function __construct(
        private readonly string $uuidClient,
        private readonly string $clientName,
        private readonly array $recipientEmails,
        private readonly int $productId,
        private readonly string $productName,
        private readonly int $scaleId,
        private readonly string $deviceId,
        private readonly float $previousWeight,
        private readonly float $currentWeight,
        private readonly float $variation,
        private readonly float $weightRange,
        private readonly string $nameUnit,
        private readonly DateTimeImmutable $occurredAt,
        private readonly bool $isHoliday,
        private readonly bool $outsideBusinessHours,
        private readonly ?float $conversionFactor = null
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

    public function getPreviousWeight(): float
    {
        return $this->previousWeight;
    }

    public function getCurrentWeight(): float
    {
        return $this->currentWeight;
    }

    public function getVariation(): float
    {
        return $this->variation;
    }

    public function getWeightRange(): float
    {
        return $this->weightRange;
    }

    public function getNameUnit(): string
    {
        return $this->nameUnit;
    }

    public function getOccurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function isHoliday(): bool
    {
        return $this->isHoliday;
    }

    public function isOutsideBusinessHours(): bool
    {
        return $this->outsideBusinessHours;
    }

    public function getConversionFactor(): ?float
    {
        return $this->conversionFactor;
    }
}
