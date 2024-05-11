<?php
declare(strict_types=1);
namespace App\Client\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Client\Infrastructure\OutputAdapters\ClientRepository;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\Table(name: "clients")]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private ?string $databaseConnection = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setDatabaseConnection(string $databaseConnection): self
    {
        $this->databaseConnection = $databaseConnection;
        return $this;
    }

    public function getDatabaseConnection(): ?string
    {
        return $this->databaseConnection;
    }
}