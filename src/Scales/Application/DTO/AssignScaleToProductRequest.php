<?php

namespace App\Scales\Application\DTO;

class AssignScaleToProductRequest
{
    private string $uuidClient;
    private string $uuidScale;
    private int $productId;

    public function __construct(string $uuidClient, string $uuidScale, int $productId)
    {
        $this->uuidClient = $uuidClient;
        $this->uuidScale = $uuidScale;
        $this->productId = $productId;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getUuidScale(): string
    {
        return $this->uuidScale;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }
}