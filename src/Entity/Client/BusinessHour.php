<?php

namespace App\Entity\Client;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'business_hours')]
class BusinessHour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'day_of_week', type: 'smallint', options: ['unsigned' => true])]
    private int $dayOfWeek;

    #[ORM\Column(name: 'start_time', type: Types::TIME_MUTABLE)]
    private DateTimeInterface $startTime;

    #[ORM\Column(name: 'end_time', type: Types::TIME_MUTABLE)]
    private DateTimeInterface $endTime;

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

    public function getDayOfWeek(): int
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(int $dayOfWeek): self
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    public function getStartTime(): DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

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

    public function coversDateTime(DateTimeInterface $dateTime): bool
    {
        $currentSeconds = $this->timeToSeconds($dateTime);
        $startSeconds = $this->timeToSeconds($this->startTime);
        $endSeconds = $this->timeToSeconds($this->endTime);

        if ($startSeconds <= $endSeconds) {
            return $currentSeconds >= $startSeconds && $currentSeconds <= $endSeconds;
        }

        return $currentSeconds >= $startSeconds || $currentSeconds <= $endSeconds;
    }

    private function timeToSeconds(DateTimeInterface $time): int
    {
        return ((int) $time->format('H')) * 3600 + ((int) $time->format('i')) * 60 + (int) $time->format('s');
    }
}
