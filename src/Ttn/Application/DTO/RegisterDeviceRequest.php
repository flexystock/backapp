<?php

namespace App\Ttn\Application\DTO;

class RegisterDeviceRequest
{
    private string $deviceId;
    private ?string $devEui;
    private ?string $joinEui;
    private ?string $appKey;
    // Otros campos según sea necesario

    public function __construct(string $deviceId, ?string $devEui = null, ?string $joinEui = null, ?string $appKey = null)
    {
        $this->deviceId = $deviceId;
        $this->devEui = $devEui;
        $this->joinEui = $joinEui;
        $this->appKey = $appKey;
    }

    public function getDeviceId(): string
    {
        return $this->deviceId;
    }

    public function getDevEui(): ?string
    {
        return $this->devEui;
    }

    public function getJoinEui(): ?string
    {
        return $this->joinEui;
    }

    public function getAppKey(): ?string
    {
        return $this->appKey;
    }
}