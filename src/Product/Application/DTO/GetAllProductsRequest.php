<?php

// src/Product/Application/DTO/GetAllProductsRequest.php

namespace App\Product\Application\DTO;

class GetAllProductsRequest
{
    private string $uuidClient;

    public function __construct(string $uuidClient)
    {
        $this->uuidClient = $uuidClient;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }
}
