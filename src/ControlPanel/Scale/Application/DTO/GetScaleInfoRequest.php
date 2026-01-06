<?php

declare(strict_types=1);

namespace App\ControlPanel\Scale\Application\DTO;

class GetScaleInfoRequest
{
    private ?string $endDeviceId;

    public function __construct(?string $endDeviceId = null)
    {
        $this->endDeviceId = $endDeviceId;
    }

    public function getEndDeviceId(): ?string
    {
        return $this->endDeviceId;
    }
}
