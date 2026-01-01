<?php

declare(strict_types=1);

namespace App\ControlPanel\Ttn\Application\DTO;

class DeleteTtnDeviceResponse
{
    private bool $success;
    private ?string $message;
    private int $statusCode;

    public function __construct(bool $success, ?string $message = null, int $statusCode = 200)
    {
        $this->success = $success;
        $this->message = $message;
        $this->statusCode = $statusCode;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
