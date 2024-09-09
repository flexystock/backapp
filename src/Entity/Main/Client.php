<?php
namespace App\Entity\Main;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity(repositoryClass: App\Repository\ClientRepository::class)]
#[ORM\Table(name: 'client')]
class Client
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, unique: true, options: ['fixed' => true])]
    private string $uuid_client;

    #[ORM\Column(type: 'string', length: 50, unique: true, nullable: false)]
    private string $clientName;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $databaseName= null;

    public function __construct()
    {
        $this->uuid_client = Uuid::v4()->toRfc4122();
    }

    public function getUuidClient(): ?string
    {
        return $this->uuid_client;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid_client = $uuid;
        return $this;
    }

    public function getClientName(): ?string
    {
        return $this->clientName;
    }

    public function setName(string $name): self
    {
        $this->clientName = $name;
        return $this;
    }

    public function getDatabaseName(): ?string
    {
        return $this->databaseName;
    }
    public function setDatabaseName(string $databaseName): self
    {
        $this->databaseName = $databaseName;
        return $this;
    }
}

