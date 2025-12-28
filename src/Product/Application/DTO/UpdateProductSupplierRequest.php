<?php

namespace App\Product\Application\DTO;

class UpdateProductSupplierRequest
{
    private string $uuidClient;
    private int $productId;
    private int $clientSupplierId;
    private bool $isPreferred;
    private ?string $productCode;
    private ?float $unitPrice;
    private ?float $minOrderQuantity;
    private ?int $deliveryDays;
    private ?string $notes;

    public function __construct(
        string $uuidClient,
        int $productId,
        int $clientSupplierId,
        bool $isPreferred = false,
        ?string $productCode = null,
        ?float $unitPrice = null,
        ?float $minOrderQuantity = null,
        ?int $deliveryDays = null,
        ?string $notes = null
    ) {
        $this->uuidClient = $uuidClient;
        $this->productId = $productId;
        $this->clientSupplierId = $clientSupplierId;
        $this->isPreferred = $isPreferred;
        $this->productCode = $productCode;
        $this->unitPrice = $unitPrice;
        $this->minOrderQuantity = $minOrderQuantity;
        $this->deliveryDays = $deliveryDays;
        $this->notes = $notes;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getClientSupplierId(): int
    {
        return $this->clientSupplierId;
    }

    public function isPreferred(): bool
    {
        return $this->isPreferred;
    }

    public function getProductCode(): ?string
    {
        return $this->productCode;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function getMinOrderQuantity(): ?float
    {
        return $this->minOrderQuantity;
    }

    public function getDeliveryDays(): ?int
    {
        return $this->deliveryDays;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }
}
