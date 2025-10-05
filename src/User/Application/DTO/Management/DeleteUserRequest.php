<?php

declare(strict_types=1);

namespace App\User\Application\DTO\Management;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteUserRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_UUID_CLIENT')]
    #[Assert\Uuid(message: 'INVALID_UUID_CLIENT')]
    #[SerializedName('uuidClient')]
    private string $uuidClient;

    #[Assert\NotBlank(message: 'REQUIRED_EMAIL')]
    #[Assert\Email(message: 'INVALID_EMAIL')]
    #[SerializedName('userEmail')]
    private string $userEmail;

    public function __construct(string $uuidClient, string $userEmail)
    {
        $this->uuidClient = $uuidClient;
        $this->userEmail = $userEmail;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }
}
