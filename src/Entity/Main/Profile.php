<?php
declare(strict_types=1);

namespace App\Entity\Main;

use Doctrine\ORM\Mapping as ORM;
use App\Profile\Repository\ProfileRepository;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
#[ORM\Table(name: 'profiles')]
class Profile
{
    #[ORM\Id]
    #[ORM\Column(type: 'int')]
    private int $id;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'string', length: 50)]
    private string $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }
}
