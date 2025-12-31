<?php

namespace App\ControlPanel\User\Application\DTO;

class GetUserInfoRequest
{
    private ?string $uuidUser;

    public function __construct(?string $uuidUser = null)
    {
        $this->uuidUser = $uuidUser;
    }

    public function getUuidUser(): ?string
    {
        return $this->uuidUser;
    }
}
