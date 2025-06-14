<?php

namespace App\Scales\Application\DTO;

class DeleteScaleResponse
{
    private ?string $message;
    private ?string $error;
    private int $statusCode;

    public function __construct(?string $message, ?string $error, int $statusCode)
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
