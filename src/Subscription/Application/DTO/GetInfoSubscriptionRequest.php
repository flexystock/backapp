<?php

namespace App\Subscription\Application\DTO;

class GetInfoSubscriptionRequest
{
    private ?string $uuidSubscription;

    public function __construct(?string $uuidSubscription = null)
    {
        $this->uuidSubscription = $uuidSubscription;
    }

    public function getUuidSubscription(): ?string
    {
        return $this->uuidSubscription;
    }
}
