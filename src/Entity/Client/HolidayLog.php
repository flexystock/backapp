<?php

namespace App\Entity\Client;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'log_holidays')]
class HolidayLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'uuid_client', type: 'string', length: 36)]
    private string $uuidClient;

    #[ORM\Column(name: 'uuid_user_modification', type: 'string', length: 36)]
    private string $uuidUserModification;

    #[ORM\Column(name: 'data_before_modification', type: 'text')]
    private string $dataBeforeModification;

    #[ORM\Column(name: 'data_after_modification', type: 'text')]
    private string $dataAfterModification;

    #[ORM\Column(name: 'date_modification', type: Types::DATETIME_MUTABLE)]
    private DateTimeInterface $dateModification;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function setUuidClient(string $uuidClient): self
    {
        $this->uuidClient = $uuidClient;

        return $this;
    }

    public function getUuidUserModification(): string
    {
        return $this->uuidUserModification;
    }

    public function setUuidUserModification(string $uuidUserModification): self
    {
        $this->uuidUserModification = $uuidUserModification;

        return $this;
    }

    public function getDataBeforeModification(): string
    {
        return $this->dataBeforeModification;
    }

    public function setDataBeforeModification(string $dataBeforeModification): self
    {
        $this->dataBeforeModification = $dataBeforeModification;

        return $this;
    }

    public function getDataAfterModification(): string
    {
        return $this->dataAfterModification;
    }

    public function setDataAfterModification(string $dataAfterModification): self
    {
        $this->dataAfterModification = $dataAfterModification;

        return $this;
    }

    public function getDateModification(): DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setDateModification(DateTimeInterface $dateModification): self
    {
        $this->dateModification = $dateModification;

        return $this;
    }
}
