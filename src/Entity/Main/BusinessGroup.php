<?php

namespace App\Entity\Main;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'business_group')]
class BusinessGroup
{
    #[ORM\Id]
    #[ORM\Column(name: 'uuid_business_group', type: 'string', length: 36)]
    private string $uuidBusinessGroup;

    #[ORM\Column(type: 'string', length: 100)]
    private string $name;

    #[ORM\Column(type: 'boolean')]
    private string $active;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'businessGroups')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getUuidBusinessGroup(): string
    {
        return $this->uuidBusinessGroup;
    }

    public function setUuidBusinessGroup(string $uuidBusinessGroup): void
    {
        $this->uuidBusinessGroup = $uuidBusinessGroup;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getActive(): string
    {
        return $this->active;
    }

    public function setActive(string $active): void
    {
        $this->active = $active;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function setUsers(Collection $users): void
    {
        $this->users = $users;
    }
}
