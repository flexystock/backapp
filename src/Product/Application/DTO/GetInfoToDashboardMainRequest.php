<?php

// src/Product/Application/DTO/GetInfoToDashboardMainRequest.php

namespace App\Product\Application\DTO;

class GetInfoToDashboardMainRequest
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
