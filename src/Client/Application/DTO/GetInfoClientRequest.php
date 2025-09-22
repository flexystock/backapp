<?php

namespace App\Client\Application\DTO;

class GetInfoClientRequest
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