<?php

declare(strict_types=1);

namespace App\User\Application\DTO\Management;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserRoleRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_UUID_CLIENT')]
    #[Assert\Uuid(message: 'INVALID_UUID_CLIENT')]
    #[SerializedName('uuidClient')]
    private string $uuidClient;

    #[Assert\NotBlank(message: 'REQUIRED_EMAIL')]
    #[Assert\Email(message: 'INVALID_EMAIL')]
    #[SerializedName('userEmail')]
    private string $userEmail;

    #[Assert\NotBlank(message: 'REQUIRED_ROLE')]
    #[SerializedName('userRol')]
    private string $userRol;

    #[Assert\NotBlank(message: 'REQUIRED_UUID_USER_MODIFICATION')]
    #[Assert\Uuid(message: 'INVALID_UUID_USER_MODIFICATION')]
    #[SerializedName('uuidUserModification')]
    private string $uuidUserModification;

    public function __construct(string $uuidClient, string $userEmail, string $userRol, string $uuidUserModification)
    {
        $this->uuidClient = $uuidClient;
        $this->userEmail = $userEmail;
        $this->userRol = $userRol;
        $this->uuidUserModification = $uuidUserModification;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function getUserRol(): string
    {
        return $this->userRol;
    }

    public function getUuidUserModification(): string
    {
        return $this->uuidUserModification;
    }
}
