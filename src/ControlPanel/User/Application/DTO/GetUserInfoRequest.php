<?php

declare(strict_types=1);

namespace App\ControlPanel\User\Application\DTO;

class GetUserInfoRequest
{
    private ?string $emailUser;

    public function __construct(?string $emailUser = null)
    {
        $this->emailUser = $emailUser;
    }

    public function getEmailUser(): ?string
    {
        return $this->emailUser;
    }
}
