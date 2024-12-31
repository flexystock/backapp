<?php

namespace App\Ttn\Application\DTO;

class RegisterTtnDeviceRequest
{
    private string $deviceId;
    private string $uuidUser;
    private \DateTimeInterface $datehourCreation;
    private ?string $uuidClient;
    private ?string $devEui;
    private ?string $joinEui;
    private ?string $appKey;
    // Otros campos según sea necesario

    public function __construct(string $deviceId, string $uuidUser,
        \DateTimeInterface $datehourCreation, ?string $uuidClient = null, ?string $devEui = null,
        ?string $joinEui = null, ?string $appKey = null,
    ) {
        $this->deviceId = $deviceId;
        $this->uuidUser = $uuidUser;
        $this->datehourCreation = $datehourCreation;
        $this->uuidClient = $uuidClient;
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

    public function getUuidUser(): string
    {
        return $this->uuidUser;
    }

    public function getDatehourCreation(): \DateTimeInterface
    {
        return $this->datehourCreation;
    }
    public function getUuidClient(): ?string
    {
        return $this->uuidClient;
    }
}
