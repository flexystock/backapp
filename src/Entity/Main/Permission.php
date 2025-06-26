<?php

declare(strict_types=1);

namespace App\Entity\Main;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'permissions')]
class Permission
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue]
    private int $id;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private string $name;

    #[ORM\OneToMany(targetEntity: ProfilePermission::class, mappedBy: 'permission')]
    private Collection $profilePermissions;

    public function __construct()
    {
        $this->profilePermissions = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
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
