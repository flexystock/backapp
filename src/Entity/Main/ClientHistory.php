<?php

namespace App\Entity\Main;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'client_history')]
class ClientHistory
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private int $id;

    #[ORM\Column(type: 'string', length: 36)]
    private string $uuid_client;

    #[ORM\Column(type: 'string', length: 36)]
    private string $uuid_user_modification;

    #[ORM\Column(type: 'text')]
    private string $data_client_before_modification;

    #[ORM\Column(type: 'text')]
    private string $data_client_after_modification;

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

    public function getUuidClient(): string
    {
        return $this->uuid_client;
    }

    public function setUuidClient(string $uuidClient): void
    {
        $this->uuid_client = $uuidClient;
    }

    public function getUuidUserModification(): string
    {
        return $this->uuid_user_modification;
    }

    public function setUuidUserModification(string $uuidUserModification): void
    {
        $this->uuid_user_modification = $uuidUserModification;
    }

    public function getDataClientBeforeModification(): string
    {
        return $this->data_client_before_modification;
    }

    public function setDataClientBeforeModification(string $dataClientBeforeModification): void
    {
        $this->data_client_before_modification = $dataClientBeforeModification;
    }

    public function getDataClientAfterModification(): string
    {
        return $this->data_client_after_modification;
    }

    public function setDataClientAfterModification(string $dataClientAfterModification): void
    {
        $this->data_client_after_modification = $dataClientAfterModification;
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