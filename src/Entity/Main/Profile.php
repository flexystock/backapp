<?php

declare(strict_types=1);

namespace App\Entity\Main;

use App\Profile\Repository\ProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
#[ORM\Table(name: 'profiles')]
class Profile
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'string', length: 50)]
    private string $description;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'profile')]
    private Collection $users;

    #[ORM\OneToMany(targetEntity: ProfilePermission::class, mappedBy: 'profile')]
    private Collection $profilePermissions;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->profilePermissions = new ArrayCollection();
    }

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

    public function getProfilePermissions(): Collection
    {
        return $this->profilePermissions;
    }

    public function setProfilePermissions(Collection $profilePermissions): void
    {
        $this->profilePermissions = $profilePermissions;
    }
}
