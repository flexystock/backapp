<?php

namespace App\Entity\Client;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'holidays')]
class Holiday
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'holiday_date', type: Types::DATE_MUTABLE, unique: true)]
    private DateTimeInterface $holidayDate;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    private ?string $name = null;

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

    public function getHolidayDate(): DateTimeInterface
    {
        return $this->holidayDate;
    }

    public function setHolidayDate(DateTimeInterface $holidayDate): self
    {
        $this->holidayDate = $holidayDate;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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
