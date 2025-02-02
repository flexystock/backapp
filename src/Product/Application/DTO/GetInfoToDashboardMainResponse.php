<?php
// src/Product/Application/DTO/GetInfoToDashboardMainResponse.php

namespace App\Product\Application\DTO;

class GetInfoToDashboardMainResponse
{
    private ?array $product;
    private ?string $error;
    private int $statusCode;

    public function __construct(?array $product, ?string $error, int $statusCode)
    {
        $this->product = $product;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getProduct(): ?array
    {
        return $this->product;
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