<?php

namespace App\Scales\Application\DTO;

class AssignScaleToProductRequest
{
    private string $uuidClient;
    private string $endDeviceId;
    private int $productId;
    private string $uuidUser;

    public function __construct(string $uuidClient, string $endDeviceId, int $productId, string $uuidUser)
    {
        $this->uuidClient = $uuidClient;
        $this->endDeviceId = $endDeviceId;
        $this->productId = $productId;
        $this->uuidUser = $uuidUser;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getEndDeviceId(): string
    {
        return $this->endDeviceId;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }
    public function getUuidUserCreation(): string
    {
        return $this->uuidUser;
    }
}