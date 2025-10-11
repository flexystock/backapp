<?php

namespace App\User\Application\DTO\Profile;

class GetUserInfoRequest
{
    private string $uuidClient;
    private string $uuidUser;

    public function __construct(string $uuidClient, string $uuidUser)
    {
        $this->uuidClient = $uuidClient;
        $this->uuidUser = $uuidUser;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getUuidUser(): string
    {
        return $this->uuidUser;
    }
}
