<?php

namespace App\Entity\Main;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'password_reset')]
class PasswordReset
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true)]
    private string $id;
    #[ORM\Column(type: 'string', length: 255)]
    private string $email;
    #[ORM\Column(type: 'string', length: 255)]
    private string $token_hash;
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $expires_at;

    public function __construct(string $email, string $token, \DateTimeImmutable $expiresAt)
    {
        $this->id = uuid_create();
        $this->email = $email;
        $this->token_hash = password_hash($token, PASSWORD_DEFAULT);
        $this->expires_at = $expiresAt;
    }

    public function verifyToken(string $token): bool
    {
        return password_verify($token, $this->token_hash) && $this->expires_at > new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getTokenHash(): string
    {
        return $this->token_hash;
    }

    public function setTokenHash(string $tokenHash): void
    {
        $this->token_hash = $tokenHash;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expires_at;
    }

    public function setExpiresAt(\DateTimeImmutable $expiresAt): void
    {
        $this->expires_at = $expiresAt;
    }

}