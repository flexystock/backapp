<?php

namespace App\Subscription\Application\OutputPorts;

use App\Entity\Main\Subscription;
use App\Entity\Main\Client;

interface SubscriptionRepositoryInterface
{
    public function save(Subscription $subscription): void;

    public function remove(Subscription $subscription): void;

    public function findByUuid(string $uuid): ?Subscription;

    public function findByUuidClient(string $uuid): ?Subscription;

    public function findByStripeSubscriptionId(string $stripeSubscriptionId): ?Subscription;

    public function findActiveByClient(Client $client): array;

    /**
     * @return Subscription[]
     */
    public function findAll(): array;
}
