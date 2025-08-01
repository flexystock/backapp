<?php

namespace App\Subscription\Application\OutputPorts;

use App\Entity\Main\SubscriptionPlan;

interface SubscriptionPlanRepositoryInterface
{
    public function save(SubscriptionPlan $subscriptionPlan): void;

    public function remove(SubscriptionPlan $subscriptionPlan): void;

    public function findByUuid(string $id): ?SubscriptionPlan;

    public function findByName(string $name): ?SubscriptionPlan;

    /**
     * @return SubscriptionPlan[]
     */
    public function findAll(): array;

}
