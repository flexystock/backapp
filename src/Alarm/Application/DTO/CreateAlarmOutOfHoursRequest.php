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

    #[Assert\NotNull(message: 'REQUIRED_CHECK_OUT_OF_HOURS')]
    #[Assert\Type(type: 'integer', message: 'INVALID_CHECK_OUT_OF_HOURS')]
    #[Assert\Choice(choices: [0, 1], message: 'INVALID_CHECK_OUT_OF_HOURS')]
    private int $checkOutOfHours;

    private ?string $uuidUser = null;

    private ?\DateTimeInterface $timestamp = null;

    public function __construct(string $uuidClient, array $businessHours, int $checkOutOfHours)
    {
        $this->uuidClient = $uuidClient;
        $this->businessHours = $businessHours;
        $this->checkOutOfHours = $checkOutOfHours;
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

    public function getCheckOutOfHours(): int
    {
        return $this->checkOutOfHours;
    }

    public function isCheckOutOfHoursEnabled(): bool
    {
        return 1 === $this->checkOutOfHours;
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
