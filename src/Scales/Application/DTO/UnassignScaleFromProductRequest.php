<?php

namespace App\Scales\Application\DTO;

class UnassignScaleFromProductRequest
{
    private string $uuidClient;
    private string $endDeviceId;
    private string $uuidUser;

    public function __construct(string $uuidClient, string $endDeviceId, string $uuidUser)
    {
        $this->uuidClient = $uuidClient;
        $this->endDeviceId = $endDeviceId;
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

    public function getUuidUser(): string
    {
        return $this->uuidUser;
    }
}
