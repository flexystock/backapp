<?php

// src/Ttn/Application/DTO/RegisterDeviceResponse.php

namespace App\Ttn\Application\DTO;

class GetAppsTtnResponse
{
    private bool $success;
    private ?string $error;

    public function __construct(bool $success, ?string $error = null)
    {
        $this->success = $success;
        $this->error = $error;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}