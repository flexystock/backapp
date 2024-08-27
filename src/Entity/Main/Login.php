<?php

namespace App\Entity\Main;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'login')]
class Login
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: 'string', length: 36)]
    private string $uuidUser;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $login_at;

    #[ORM\Column(type: 'string', length: 45)]
    private string $ip_address;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuidUser(): ?string
    {
        return $this->uuidUser;
    }

    public function setUuidUser(string $uuidUser): self
    {
        $this->uuidUser = $uuidUser;
        return $this;
    }

    public function getLoginAt(): \DateTimeInterface
    {
        return $this->login_at;
    }

    public function setLoginAt(\DateTimeInterface $loginAt): self
    {
        $this->login_at = $loginAt;
        return $this;
    }

    public function getIpAddress(): string
    {
        return $this->ip_address;
    }

    public function setIpAddress(string $ip_address): self
    {
        $this->ip_address = $ip_address;
        return $this;
    }
}
