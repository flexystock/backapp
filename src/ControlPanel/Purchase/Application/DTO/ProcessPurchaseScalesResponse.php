<?php

declare(strict_types=1);

namespace App\ControlPanel\Purchase\Application\DTO;

class ProcessPurchaseScalesResponse
{
    private bool $success;
    private string $message;
    private array $devicesCreated;

    public function __construct(bool $success, string $message, array $devicesCreated = [])
    {
        $this->success = $success;
        $this->message = $message;
        $this->devicesCreated = $devicesCreated;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDevicesCreated(): array
    {
        return $this->devicesCreated;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'devices_created' => $this->devicesCreated,
        ];
    }
}
