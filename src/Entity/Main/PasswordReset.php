<?php

namespace App\Entity\Main;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'password_reset')]
#[ORM\UniqueConstraint(name: 'mail_UNIQUE', columns: ['email'])]
class PasswordReset
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true)]
    private string $id;
    #[ORM\Column(type: 'string', length: 255)]
    private string $email;
    #[ORM\Column(type: 'string', length: 255)]
    private string $tokenHash;
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $expiresAt;

    public function __construct(string $email, string $token, \DateTimeImmutable $expiresAt)
    {
        $this->id = uuid_create();
        $this->email = $email;
        $this->tokenHash = password_hash($token, PASSWORD_DEFAULT);
        $this->expiresAt = $expiresAt;
    }

    public function verifyToken(string $token): bool
    {
        return password_verify($token, $this->tokenHash) && $this->expiresAt > new \DateTimeImmutable();
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
        return $this->tokenHash;
    }

    public function setTokenHash(string $tokenHash): void
    {
        $this->tokenHash = $tokenHash;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeImmutable $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

}