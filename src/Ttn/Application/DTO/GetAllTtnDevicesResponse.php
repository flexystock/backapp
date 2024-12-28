<?php

namespace App\Ttn\Application\DTO;

class GetAllTtnDevicesResponse
{
    private bool $success;
    private ?string $error;
    private array $devices;
    private array $meta;

    public function __construct(bool $success,
        ?string $error,
        array $devices,
        array $meta = [])
    {
        $this->success = $success;
        $this->error = $error;
        $this->devices = $devices;
        $this->meta = $meta;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getDevices(): array
    {
        return $this->devices;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }
}
