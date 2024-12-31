<?php

namespace App\Scales\Application\DTO;

class RegisterScalesRequest
{
    private string $uuidClient;
    private string $endDeviceId;
    private ?float $voltageMin = null;
    private ?string $uuidUserCreation = null;

    public function __construct(
        string $uuidClient,
        string $endDeviceId,
        ?float $voltageMin = null,
        ?string $uuidUserCreation = null,
        // etc. si requieres más campos
    ) {
        $this->uuidUserCreation = $uuidUserCreation;
        $this->voltageMin = $voltageMin;
        $this->endDeviceId = $endDeviceId;
        $this->uuidClient = $uuidClient;
        $this->uuidClient = $uuidClient;
        $this->endDeviceId = $endDeviceId;
        $this->voltageMin = $voltageMin;
        $this->uuidUserCreation = $uuidUserCreation;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getEndDeviceId(): string
    {
        return $this->endDeviceId;
    }

    public function getVoltageMin(): ?float
    {
        return $this->voltageMin;
    }

    public function getUuidUserCreation(): ?string
    {
        return $this->uuidUserCreation;
    }

    // etc...
}
