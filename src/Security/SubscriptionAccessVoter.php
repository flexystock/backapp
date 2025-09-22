<?php

namespace App\Security;

use App\Entity\Main\Client;
use App\Subscription\Application\Services\SubscriptionWebhookService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SubscriptionAccessVoter extends Voter
{
    public const ACCESS_CLIENT = 'ACCESS_CLIENT';

    private SubscriptionWebhookService $subscriptionService;

    public function __construct(SubscriptionWebhookService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::ACCESS_CLIENT === $attribute && $subject instanceof Client;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // $attribute y $token no se usan en esta implementación
        /** @var Client $client */
        $client = $subject;

        // Verificar si el cliente tiene una suscripción activa
        return $this->subscriptionService->hasActiveSubscription($client->getUuidClient());
    }
}
