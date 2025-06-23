<?php

namespace App\Scales\Application\DTO;

class AssignScaleToProductRequest
{
    private string $uuidClient;
    private string $endDeviceId;
    private int $productId;

    public function __construct(string $uuidClient, string $endDeviceId, int $productId)
    {
        $this->uuidClient = $uuidClient;
        $this->endDeviceId = $endDeviceId;
        $this->productId = $productId;
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
}