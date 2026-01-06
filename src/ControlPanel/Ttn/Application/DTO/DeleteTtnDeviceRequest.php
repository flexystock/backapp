<?php

declare(strict_types=1);

namespace App\ControlPanel\Ttn\Application\DTO;

class DeleteTtnDeviceRequest
{
    private string $endDeviceId;

    public function __construct(string $endDeviceId)
    {
        $this->endDeviceId = $endDeviceId;
    }

    public function getEndDeviceId(): string
    {
        return $this->endDeviceId;
    }
}
