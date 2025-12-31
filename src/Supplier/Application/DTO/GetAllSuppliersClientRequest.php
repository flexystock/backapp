<?php

namespace App\Supplier\Application\DTO;

class GetAllSuppliersClientRequest
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
