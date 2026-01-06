<?php

declare(strict_types=1);

namespace App\ControlPanel\User\Application\DTO;

class GetUserInfoResponse
{
    private ?array $usersInfo;
    private ?string $error;
    private int $statusCode;

    public function __construct(?array $usersInfo = null, ?string $error = null, int $statusCode = 200)
    {
        $this->usersInfo = $usersInfo;
        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    public function getUsersInfo(): ?array
    {
        return $this->usersInfo;
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
