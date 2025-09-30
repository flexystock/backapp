<?php

namespace App\Alarm\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateAlarmOutOfHoursRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_CLIENT_ID')]
    #[Assert\Uuid(message: 'INVALID_CLIENT_ID')]
    private string $uuidClient;

    #[Assert\NotNull(message: 'REQUIRED_BUSINESS_HOURS')]
    #[Assert\Type(type: 'array', message: 'INVALID_BUSINESS_HOURS')]
    private array $businessHours;

    private ?string $uuidUser = null;

    private ?\DateTimeInterface $timestamp = null;

    public function __construct(string $uuidClient, array $businessHours)
    {
        $this->uuidClient = $uuidClient;
        $this->businessHours = $businessHours;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getBusinessHours(): array
    {
        return $this->businessHours;
    }

    public function setBusinessHours(array $businessHours): void
    {
        $this->businessHours = $businessHours;
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
