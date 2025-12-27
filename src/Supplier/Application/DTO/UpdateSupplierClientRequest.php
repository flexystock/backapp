<?php

namespace App\Supplier\Application\DTO;

class UpdateSupplierClientRequest
{
    private string $uuidClient;
    private int $supplierId;
    private ?string $email;
    private ?string $phone;
    private ?string $contactPerson;
    private int $deliveryDays;
    private ?string $address;
    private bool $integrationEnabled;
    private ?array $integrationConfig;
    private ?string $notes;
    private ?string $internalCode;
    private bool $isActive;

    public function __construct(
        string $uuidClient,
        int $supplierId,
        ?string $email = null,
        ?string $phone = null,
        ?string $contactPerson = null,
        int $deliveryDays = 2,
        ?string $address = null,
        bool $integrationEnabled = false,
        ?array $integrationConfig = null,
        ?string $notes = null,
        ?string $internalCode = null,
        bool $isActive = true
    ) {
        $this->uuidClient = $uuidClient;
        $this->supplierId = $supplierId;
        $this->email = $email;
        $this->phone = $phone;
        $this->contactPerson = $contactPerson;
        $this->deliveryDays = $deliveryDays;
        $this->address = $address;
        $this->integrationEnabled = $integrationEnabled;
        $this->integrationConfig = $integrationConfig;
        $this->notes = $notes;
        $this->internalCode = $internalCode;
        $this->isActive = $isActive;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getSupplierId(): int
    {
        return $this->supplierId;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getContactPerson(): ?string
    {
        return $this->contactPerson;
    }

    public function getDeliveryDays(): int
    {
        return $this->deliveryDays;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function isIntegrationEnabled(): bool
    {
        return $this->integrationEnabled;
    }

    public function getIntegrationConfig(): ?array
    {
        return $this->integrationConfig;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function getInternalCode(): ?string
    {
        return $this->internalCode;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
