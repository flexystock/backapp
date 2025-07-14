<?php

namespace App\Subscription\Application\DTO;

class UpdateSubscriptionRequest
{
    private string $uuidSubscription;
    private string $uuidClient;
    private ?int $planId = null;
    private ?\DateTimeInterface $endedAt = null;
    private ?bool $isActive = null;

    public function __construct(string $uuidSubscription)
    {
        $this->uuidSubscription = $uuidSubscription;
    }

    public function getUuidSubscription(): string
    {
        return $this->uuidSubscription;
    }

    public function getPlanId(): ?int
    {
        return $this->planId;
    }

    public function setPlanId(?int $planId): void
    {
        $this->planId = $planId;
    }

    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->endedAt;
    }

    public function setEndedAt(?\DateTimeInterface $endedAt): void
    {
        $this->endedAt = $endedAt;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function getUuidClient(): string
    {
        return $this->uuidClient;
    }
}
