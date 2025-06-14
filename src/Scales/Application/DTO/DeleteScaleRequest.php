<?php

namespace App\Scales\Application\DTO;

class DeleteScaleRequest
{
    private string $uuidClient;
    private string $uuidScale;

    public function __construct(string $uuidClient, string $uuidScale)
    {
        $this->uuidClient = $uuidClient;
        $this->uuidScale = $uuidScale;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getUuidScale(): string
    {
        return $this->uuidScale;
    }
}
