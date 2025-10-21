<?php

namespace App\Entity\Client;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'cliente_config')]
class ClientConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'check_out_of_hours', type: 'boolean', options: ['default' => false])]
    private bool $checkOutOfHours = false;

    #[ORM\Column(name: 'check_holidays', type: 'boolean', options: ['default' => false])]
    private bool $checkHolidays = false;

    #[ORM\Column(name: 'check_battery_shelve', type: 'boolean', options: ['default' => false])]
    private bool $checkBatteryShelve = false;

    #[ORM\Column(name: 'uuid_user_creation', type: 'string', length: 36)]
    private string $uuidUserCreation;

    #[ORM\Column(name: 'datehour_creation', type: Types::DATETIME_MUTABLE)]
    private DateTimeInterface $datehourCreation;

    #[ORM\Column(name: 'uuid_user_modification', type: 'string', length: 36, nullable: true)]
    private ?string $uuidUserModification = null;

    #[ORM\Column(name: 'datehour_modification', type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $datehourModification = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isCheckOutOfHours(): bool
    {
        return $this->checkOutOfHours;
    }

    public function setCheckOutOfHours(bool $checkOutOfHours): self
    {
        $this->checkOutOfHours = $checkOutOfHours;

        return $this;
    }

    public function isCheckHolidays(): bool
    {
        return $this->checkHolidays;
    }

    public function setCheckHolidays(bool $checkHolidays): self
    {
        $this->checkHolidays = $checkHolidays;

        return $this;
    }

    public function isCheckBatteryShelve(): bool
    {
        return $this->checkBatteryShelve;
    }

    public function setCheckBatteryShelve(bool $checkBatteryShelve): self
    {
        $this->checkBatteryShelve = $checkBatteryShelve;

        return $this;
    }

    public function getUuidUserCreation(): string
    {
        return $this->uuidUserCreation;
    }

    public function setUuidUserCreation(string $uuidUserCreation): self
    {
        $this->uuidUserCreation = $uuidUserCreation;

        return $this;
    }

    public function getDatehourCreation(): DateTimeInterface
    {
        return $this->datehourCreation;
    }

    public function setDatehourCreation(DateTimeInterface $datehourCreation): self
    {
        $this->datehourCreation = $datehourCreation;

        return $this;
    }

    public function getUuidUserModification(): ?string
    {
        return $this->uuidUserModification;
    }

    public function setUuidUserModification(?string $uuidUserModification): self
    {
        $this->uuidUserModification = $uuidUserModification;

        return $this;
    }

    public function getDatehourModification(): ?DateTimeInterface
    {
        return $this->datehourModification;
    }

    public function setDatehourModification(?DateTimeInterface $datehourModification): self
    {
        $this->datehourModification = $datehourModification;

        return $this;
    }
}
