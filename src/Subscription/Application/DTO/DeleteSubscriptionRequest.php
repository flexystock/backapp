<?php

namespace App\Subscription\Application\DTO;

class DeleteSubscriptionRequest
{
    private string $uuidSubscription;

    public function __construct(string $uuidSubscription)
    {
        $this->uuidSubscription = $uuidSubscription;
    }

    public function getUuidSubscription(): string
    {
        return $this->uuidSubscription;
    }
}
