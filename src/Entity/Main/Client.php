<?php
namespace App\Entity\Main;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity(repositoryClass: App\Repository\ClientRepository::class)]
#[ORM\Table(name: 'clients')]
class Client
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true, options: ['fixed' => true])]
    private string $uuid;

    #[ORM\Column(type: 'string', length: 50, unique: true, nullable: false)]
    private string $name;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $server = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $exclusiveHost = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $exclusiveUser = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $exclusivePassword = null;

    #[ORM\Column(type: 'string', length: 250, nullable: false)]
    private string $scheme;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $active = true;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $ttnSecureByIp = false;

    public function __construct()
    {
        $this->uuid = Uuid::v4()->toRfc4122();
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getServer(): ?string
    {
        return $this->server;
    }

    public function setServer(?string $server): self
    {
        $this->server = $server;
        return $this;
    }

    public function getExclusiveHost(): ?string
    {
        return $this->exclusiveHost;
    }

    public function setExclusiveHost(?string $exclusiveHost): self
    {
        $this->exclusiveHost = $exclusiveHost;
        return $this;
    }

    public function getExclusiveUser(): ?string
    {
        return $this->exclusiveUser;
    }

    public function setExclusiveUser(?string $exclusiveUser): self
    {
        $this->exclusiveUser = $exclusiveUser;
        return $this;
    }

    public function getExclusivePassword(): ?string
    {
        return $this->exclusivePassword;
    }

    public function setExclusivePassword(?string $exclusivePassword): self
    {
        $this->exclusivePassword = $exclusivePassword;
        return $this;
    }

    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    public function setScheme(string $scheme): self
    {
        $this->scheme = $scheme;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function isTtnSecureByIp(): ?bool
    {
        return $this->ttnSecureByIp;
    }

    public function setTtnSecureByIp(bool $ttnSecureByIp): self
    {
        $this->ttnSecureByIp = $ttnSecureByIp;
        return $this;
    }
}

