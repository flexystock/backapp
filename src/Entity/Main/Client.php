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
    private string $uuid;

    #[ORM\Column(type: 'string', length: 50, unique: true, nullable: false)]
    private string $clientName;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $databaseName= null;


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
        return $this->clientName;
    }

    public function setName(string $name): self
    {
        $this->clientName = $name;
        return $this;
    }
}

