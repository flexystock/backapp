<?php

declare(strict_types=1);

namespace App\ControlPanel\Scale\Application\DTO;

class GetScaleInfoResponse
{
    private ?array $scalesInfo;
    private ?string $error;
    private int $statusCode;

    public function __construct(?array $scalesInfo = null, ?string $error = null, int $statusCode = 200)
    {
        $this->scalesInfo = $scalesInfo;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getScalesInfo(): ?array
    {
        return $this->scalesInfo;
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
