<?php

declare(strict_types=1);

namespace App\ControlPanel\Scale\Application\DTO;

class ActivateDeactivateScaleResponse
{
    private ?string $message;
    private ?string $error;
    private int $statusCode;

    public function __construct(?string $message = null, ?string $error = null, int $statusCode = 200)
    {
        $this->message = $message;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
