<?php

declare(strict_types=1);

namespace App\ControlPanel\Scale\Application\DTO;

class ActivateDeactivateScaleRequest
{
    private string $clientName;
    private string $endDeviceId;
    private bool $active;

    public function __construct(string $clientName, string $endDeviceId, bool $active)
    {
        $this->clientName = $clientName;
        $this->endDeviceId = $endDeviceId;
        $this->active = $active;
    }

    public function getClientName(): string
    {
        return $this->clientName;
    }

    public function getEndDeviceId(): string
    {
        return $this->endDeviceId;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
