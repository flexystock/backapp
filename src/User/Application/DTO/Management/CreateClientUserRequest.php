<?php

declare(strict_types=1);

namespace App\User\Application\DTO\Management;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class CreateClientUserRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_UUID_CLIENT')]
    #[Assert\Uuid(message: 'INVALID_UUID_CLIENT')]
    #[SerializedName('uuidClient')]
    private string $uuidClient;

    #[Assert\NotBlank(message: 'REQUIRED_EMAIL')]
    #[Assert\Email(message: 'INVALID_EMAIL')]
    #[SerializedName('userEmail')]
    private string $email;

    #[Assert\NotBlank(message: 'REQUIRED_ROLE')]
    #[SerializedName('userRol')]
    private string $role;

    private ?string $createdByUuid = null;

    public function __construct(string $uuidClient, string $email, string $role)
    {
        $this->uuidClient = $uuidClient;
        $this->email = $email;
        $this->role = $role;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setCreatedByUuid(?string $createdByUuid): void
    {
        $this->createdByUuid = $createdByUuid;
    }

    public function getCreatedByUuid(): ?string
    {
        return $this->createdByUuid;
    }
}
