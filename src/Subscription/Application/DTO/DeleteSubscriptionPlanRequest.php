<?php

namespace App\Subscription\Application\DTO;

class DeleteSubscriptionPlanRequest
{
    private int $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
