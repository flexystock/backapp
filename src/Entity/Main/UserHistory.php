<?php

namespace App\Entity\Main;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'user_history')]
class UserHistory
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(type: 'string', length: 36)]
    private string $uuid_user;

    #[ORM\Column(type: 'string', length: 36)]
    private string $uuid_user_modification;

    #[ORM\Column(type: 'text')]
    private string $data_user_before_modification;

    #[ORM\Column(type: 'text')]
    private string $data_user_after_modification;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $date_modification;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUuidUser(): string
    {
        return $this->uuid_user;
    }

    public function setUuidUser(string $uuidUser): void
    {
        $this->uuid_user = $uuidUser;
    }

    public function getUuidUserModification(): string
    {
        return $this->uuid_user_modification;
    }

    public function setUuidUserModification(string $uuidUserModification): void
    {
        $this->uuid_user_modification = $uuidUserModification;
    }

    public function getDataUserBeforeModification(): string
    {
        return $this->data_user_before_modification;
    }

    public function setDataUserBeforeModification(string $dataUserBeforeModification): void
    {
        $this->data_user_before_modification = $dataUserBeforeModification;
    }

    public function getDataUserAfterModification(): string
    {
        return $this->data_user_after_modification;
    }

    public function setDataUserAfterModification(string $dataUserAfterModification): void
    {
        $this->data_user_after_modification = $dataUserAfterModification;
    }

    public function getDateModification(): \DateTimeInterface
    {
        return $this->date_modification;
    }

    public function setDateModification(\DateTimeInterface $dateModification): void
    {
        $this->date_modification = $dateModification;
    }
}
