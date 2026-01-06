<?php

declare(strict_types=1);

namespace App\ControlPanel\Ttn\Application\DTO;

class AssignTtnDeviceToClientRequest
{
    private string $endDeviceId;
    private string $uuidClient;

    public function __construct(string $endDeviceId, string $uuidClient)
    {
        $this->endDeviceId = $endDeviceId;
        $this->uuidClient = $uuidClient;
    }

    public function getEndDeviceId(): string
    {
        return $this->endDeviceId;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }
}
