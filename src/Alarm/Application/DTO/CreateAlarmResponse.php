<?php

namespace App\Alarm\Application\DTO;

class CreateAlarmResponse
{
    private ?array $alarm;
    private ?string $error;
    private int $statusCode;

    public function __construct(?array $alarm, ?string $error, int $statusCode)
    {
        $this->alarm = $alarm;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getAlarm(): ?array
    {
        return $this->alarm;
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
