<?php

namespace App\Ttn\Application\DTO;

class TtnUplinkRequest
{
    private ?string $devEui;
    private ?string $deviceId;
    private ?string $joinEui;
    private ?float $voltage;
    private ?float $weight;
    private int $weightGrams;

    public function __construct(?string $devEui, ?string $deviceId,
                                ?string $joinEui, ?float $voltage, ?float $weight,
                                int $weightGrams)
    {
        $this->devEui = $devEui;
        $this->deviceId = $deviceId;
        $this->joinEui = $joinEui;
        $this->voltage = $voltage;
        $this->weight = $weight;
        $this->weightGrams = $weightGrams;
    }

    public function getDevEui(): ?string
    {
        return $this->devEui;
    }

    public function getDeviceId(): ?string
    {
        return $this->deviceId;
    }

    public function getJoinEui(): ?string
    {
        return $this->joinEui;
    }

    public function getVoltage(): ?float
    {
        return $this->voltage;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function getWeightGrams(): int  // ← NUEVO
    {
        return $this->weightGrams;
    }
}
