<?php

namespace App\Subscription\Application\Services;

use App\Entity\Main\Client;
use App\Entity\Main\Subscription;
use App\Entity\Main\SubscriptionPlan;
use App\Subscription\Application\OutputPorts\SubscriptionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

class SubscriptionDomainService
{
    private SubscriptionRepositoryInterface $subscriptionRepository;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(
        SubscriptionRepositoryInterface $subscriptionRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Create subscription entity in database.
     */
    public function createSubscription(
        Client $client,
        SubscriptionPlan $plan,
        ?\DateTimeInterface $startedAt = null,
        ?\DateTimeInterface $endedAt = null,
        ?string $stripeSubscriptionId = null,
        ?string $uuidUserCreation = null,
        string $context = 'general'
    ): Subscription {
        try {
            $subscription = new Subscription();
            $subscription->setUuidSubscription(Uuid::v4()->toRfc4122());

            // Use entity references to avoid additional queries
            $clientRef = $this->entityManager->getReference(Client::class, $client->getUuidClient());
            $planRef = $this->entityManager->getReference(SubscriptionPlan::class, $plan->getId());

            $subscription->setClient($clientRef);
            $subscription->setPlan($planRef);
            $subscription->setStartedAt($startedAt ?? new \DateTime());
            $subscription->setEndedAt($endedAt);
            $subscription->setCreatedAt(new \DateTime());
            $subscription->setUpdatedAt(new \DateTime());

            // Optional fields
            if ($stripeSubscriptionId) {
                $subscription->setStripeSubscriptionId($stripeSubscriptionId);
            }

            if ($uuidUserCreation) {
                $subscription->setUuidUserCreation($uuidUserCreation);
                $subscription->setUuidUserModification($uuidUserCreation);
            }

            // Set payment status and active state based on context
            if ('webhook' === $context) {
                $subscription->setIsActive(true);
                $subscription->setPaymentStatus('paid');
            } else {
                $subscription->setIsActive(true);
                $subscription->setPaymentStatus('pending'); // Will be updated when Stripe confirms
            }

            $this->subscriptionRepository->save($subscription);

            $this->logger->info('Subscription created via domain service', [
                'subscription_uuid' => $subscription->getUuidSubscription(),
                'client_uuid' => $client->getUuidClient(),
                'plan_id' => $plan->getId(),
                'stripe_subscription_id' => $stripeSubscriptionId ?? 'not_set',
                'context' => $context,
            ]);

            return $subscription;
        } catch (\Throwable $e) {
            $this->logger->error('Error creating subscription via domain service', [
                'client_uuid' => $client->getUuidClient(),
                'plan_id' => $plan->getId(),
                'stripe_subscription_id' => $stripeSubscriptionId ?? 'not_set',
                'context' => $context,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Create subscription from webhook with pre-existing Stripe ID.
     */
    public function createSubscriptionFromWebhook(
        Client $client,
        SubscriptionPlan $plan,
        string $stripeSubscriptionId,
        ?\DateTimeInterface $startedAt = null,
        ?\DateTimeInterface $endedAt = null
    ): Subscription {
        return $this->createSubscription(
            $client,
            $plan,
            $startedAt,
            $endedAt,
            $stripeSubscriptionId,
            null,
            'webhook'
        );
    }

    /**
     * Update subscription with proper user tracking.
     */
    public function updateSubscription(
        Subscription $subscription,
        ?string $uuidUserModification = null,
        string $context = 'general'
    ): Subscription {
        try {
            // Always update the modification timestamp
            $subscription->setUpdatedAt(new \DateTime());

            // Set user modification if provided
            if ($uuidUserModification) {
                $subscription->setUuidUserModification($uuidUserModification);
            }

            $this->subscriptionRepository->save($subscription);

            $this->logger->info('Subscription updated via domain service', [
                'subscription_uuid' => $subscription->getUuidSubscription(),
                'uuid_user_modification' => $uuidUserModification ?? 'not_set',
                'context' => $context,
            ]);

            return $subscription;
        } catch (\Throwable $e) {
            $this->logger->error('Error updating subscription via domain service', [
                'subscription_uuid' => $subscription->getUuidSubscription(),
                'uuid_user_modification' => $uuidUserModification ?? 'not_set',
                'context' => $context,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
