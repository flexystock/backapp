<?php

namespace App\Subscription\Application\DTO;

class CreateSubscriptionRequest
{
    private string $uuidClient;
    private int $planId;
    private ?\DateTimeInterface $startedAt;
    private ?\DateTimeInterface $endedAt;
    private ?string $uuidUser = null;

    public function __construct(string $uuidClient, int $planId, \DateTimeInterface $startedAt, ?\DateTimeInterface $endedAt = null, ?string $uuidUser = null)
    {
        $this->uuidClient = $uuidClient;
        $this->planId = $planId;
        $this->startedAt = $startedAt;
        $this->endedAt = $endedAt;
        $this->uuidUser = $uuidUser;
    }

    public function getClientUuid(): string
    {
        return $this->uuidClient;
    }

    public function getPlanId(): int
    {
        return $this->planId;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->endedAt;
    }

    public function getUuidUser(): ?string
    {
        return $this->uuidUser;
    }

    public function setUuidUser(?string $uuidUser): void
    {
        $this->uuidUser = $uuidUser;
    }
}
