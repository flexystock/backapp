<?php

namespace App\Subscription\Application\DTO;

class CheckSubscriptionStatusRequest
{
    private ?string $clientUuid;
    private ?string $subscriptionUuid;

    public function __construct(?string $clientUuid = null, ?string $subscriptionUuid = null)
    {
        $this->clientUuid = $clientUuid;
        $this->subscriptionUuid = $subscriptionUuid;
    }

    public function getClientUuid(): ?string
    {
        return $this->clientUuid;
    }

    public function getSubscriptionUuid(): ?string
    {
        return $this->subscriptionUuid;
    }

    public function setClientUuid(?string $clientUuid): void
    {
        $this->clientUuid = $clientUuid;
    }

    public function setSubscriptionUuid(?string $subscriptionUuid): void
    {
        $this->subscriptionUuid = $subscriptionUuid;
    }
}
