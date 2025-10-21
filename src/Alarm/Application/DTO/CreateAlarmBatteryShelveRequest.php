<?php

namespace App\Alarm\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateAlarmBatteryShelveRequest
{
    #[Assert\NotBlank(message: 'REQUIRED_CLIENT_ID')]
    #[Assert\Uuid(message: 'INVALID_CLIENT_ID')]
    private string $uuidClient;

    #[Assert\NotNull(message: 'REQUIRED_CHECK_BATTERY_SHELVE')]
    #[Assert\Type(type: 'integer', message: 'INVALID_CHECK_BATTERY_SHELVE')]
    #[Assert\Choice(choices: [0, 1], message: 'INVALID_CHECK_BATTERY_SHELVE')]
    private int $checkBatteryShelve;

    private ?string $uuidUser = null;

    private ?\DateTimeInterface $timestamp = null;

    public function __construct(string $uuidClient, int $checkBatteryShelve)
    {
        $this->uuidClient = $uuidClient;
        $this->checkBatteryShelve = $checkBatteryShelve;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }

    public function getCheckBatteryShelve(): int
    {
        return $this->checkBatteryShelve;
    }

    public function isCheckBatteryShelveEnabled(): bool
    {
        return 1 === $this->checkBatteryShelve;
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
