<?php

// src/Product/Application/DTO/GetProductRequest.php

namespace App\Product\Application\DTO;

class GetProductRequest
{
    private string $uuidClient;
    private string $uuidProduct;

    public function __construct(string $uuidClient, string $uuidProduct)
    {
        $this->uuidClient = $uuidClient;
        $this->uuidProduct = $uuidProduct;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getUuidProduct(): string
    {
        return $this->uuidProduct;
    }
}