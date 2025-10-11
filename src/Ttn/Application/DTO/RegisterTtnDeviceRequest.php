<?php

namespace App\Ttn\Application\DTO;

class RegisterTtnDeviceRequest
{
    private string $uuidUser;
    private \DateTimeInterface $datehourCreation;
    private ?string $uuidClient;
    private ?string $devEui;
    private ?string $joinEui;
    private ?string $appKey;
    private ?string $deviceId; // Opcional, si se necesita un identificador de dispositivo específico
    // Otros campos según sea necesario

    public function __construct(
        string $uuidUser,
        \DateTimeInterface $datehourCreation,
        ?string $uuidClient = null,
        ?string $devEui = null,
        ?string $joinEui = null,
        ?string $appKey = null,
        ?string $deviceId = null
    ) {
        $this->uuidUser = $uuidUser;
        $this->datehourCreation = $datehourCreation;
        $this->uuidClient = $uuidClient;
        $this->devEui = $devEui;
        $this->joinEui = $joinEui;
        $this->appKey = $appKey;
        $this->deviceId = $deviceId;
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

    public function getDeviceId(): ?string
    {
        return $this->deviceId;
    }
}
