<?php

namespace App\Alarm\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateAlarmHolidayRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_CLIENT_ID')]
    #[Assert\Uuid(message: 'INVALID_CLIENT_ID')]
    private string $uuidClient;

    #[Assert\NotBlank(message: 'REQUIRED_HOLIDAY_DATE')]
    #[Assert\Date(message: 'INVALID_HOLIDAY_DATE')]
    private string $holidayDate;

    private ?string $name;

    private ?string $uuidUser = null;

    private ?\DateTimeInterface $timestamp = null;

    public function __construct(string $uuidClient, string $holidayDate, ?string $name = null)
    {
        $this->uuidClient = $uuidClient;
        $this->holidayDate = $holidayDate;
        $this->name = $name;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getHolidayDate(): string
    {
        return $this->holidayDate;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getUuidUser(): ?string
    {
        return $this->uuidUser;
    }

    public function setUuidUser(?string $uuidUser): void
    {
        $this->uuidUser = $uuidUser;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->timestamp;
    }

    public function setTimestamp(?\DateTimeInterface $timestamp): void
    {
        $this->timestamp = $timestamp;
    }
}
