<?php

namespace App\Subscription\Application\DTO;

class CreateSubscriptionRequest
{
    private string $clientUuid;
    private int $planId;
    private \DateTimeInterface $startedAt;
    private ?\DateTimeInterface $endedAt;

    public function __construct(string $clientUuid, int $planId, \DateTimeInterface $startedAt, ?\DateTimeInterface $endedAt = null)
    {
        $this->clientUuid = $clientUuid;
        $this->planId = $planId;
        $this->startedAt = $startedAt;
        $this->endedAt = $endedAt;
    }

    public function getClientUuid(): string
    {
        return $this->clientUuid;
    }

    public function getPlanId(): int
    {
        return $this->planId;
    }

    public function getStartedAt(): \DateTimeInterface
    {
        return $this->startedAt;
    }

    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->endedAt;
    }
}
