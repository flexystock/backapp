<?php

namespace App\Subscription\Application\OutputPorts;

use App\Entity\Main\Subscription;

interface SubscriptionRepositoryInterface
{
    public function save(Subscription $subscription): void;

    public function remove(Subscription $subscription): void;

    public function findByUuid(string $uuid): ?Subscription;

    /**
     * @return Subscription[]
     */
    public function findAll(): array;
}
