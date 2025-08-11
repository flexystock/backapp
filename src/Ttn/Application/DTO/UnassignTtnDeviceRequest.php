<?php

namespace App\Ttn\Application\DTO;

class UnassignTtnDeviceRequest
{
    private string $uuidClient;
    private string $endDeviceId;
    private string $uuidUserModification;
    private \DateTimeInterface $datehourModification;

    public function __construct(string $uuidClient, string $endDeviceId, string $uuidUserModification,
        \DateTimeInterface $datehourModification)
    {
        $this->uuidClient = $uuidClient;
        $this->endDeviceId = $endDeviceId;
        $this->uuidUserModification = $uuidUserModification;
        $this->datehourModification = $datehourModification;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getEndDeviceId(): string
    {
        return $this->endDeviceId;
    }

    public function getUuidUserModification(): string
    {
        return $this->uuidUserModification;
    }

    public function getDatehourModification(): \DateTimeInterface
    {
        return $this->datehourModification;
    }
}
