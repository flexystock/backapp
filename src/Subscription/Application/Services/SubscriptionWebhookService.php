<?php

namespace App\Subscription\Application\Services;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Main\Client;
use App\Entity\Main\Subscription;
use App\Subscription\Application\OutputPorts\SubscriptionPlanRepositoryInterface;
use App\Subscription\Application\OutputPorts\SubscriptionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class SubscriptionWebhookService
{
    private SubscriptionRepositoryInterface $subscriptionRepository;
    private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository;
    private ClientRepositoryInterface $clientRepository;
    private SubscriptionDomainService $subscriptionDomainService;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(
        SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        ClientRepositoryInterface $clientRepository,
        SubscriptionDomainService $subscriptionDomainService,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
        $this->clientRepository = $clientRepository;
        $this->subscriptionDomainService = $subscriptionDomainService;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * Handle checkout.session.completed event.
     */
    public function handleCheckoutCompleted(object $checkoutSession): void
    {
        $this->logger->info(' ######################. Handling checkout.session.completed event', [
            'checkout_session' => json_decode($checkoutSession),
        ]);
        $this->logger->info(' ######################. Handling checkout.session.completed event', [
            'checkout_session' => json_encode($checkoutSession),
        ]);

        $stripeSubscriptionId = $checkoutSession->subscription ?? null;

        if (!$stripeSubscriptionId) {
            $this->logger->warning('Checkout session completed without subscription ID', [
                'checkout_session_id' => $checkoutSession->id,
            ]);

            return;
        }

        /** @var Subscription|null $subscription */
        $subscription = $this->subscriptionRepository->findByStripeSubscriptionId($stripeSubscriptionId);

        if (!$subscription) {
            // Try to create subscription automatically
            $subscription = $this->createSubscriptionFromCheckoutSession($checkoutSession, $stripeSubscriptionId);

            if (!$subscription) {
                return; // Creation failed, error already logged
            }
        }

        // Update existing subscription with proper date fields and activate it
        $this->updateSubscriptionFromCheckoutSession($subscription, $checkoutSession);

        $this->subscriptionRepository->save($subscription);

        $this->logger->info('Subscription processed after checkout completion', [
            'subscription_uuid' => $subscription->getUuidSubscription(),
            'stripe_subscription_id' => $stripeSubscriptionId,
            'client_uuid' => $subscription->getClient()->getUuidClient(),
        ]);
    }

    /**
     * Handle customer.subscription.deleted event.
     */
    public function handleSubscriptionDeleted(object $stripeSubscription): void
    {
        $stripeSubscriptionId = $stripeSubscription->id;

        /** @var Subscription|null $subscription */
        $subscription = $this->subscriptionRepository->findByStripeSubscriptionId($stripeSubscriptionId);

        if (!$subscription) {
            $this->logger->warning('Subscription not found for deletion', [
                'stripe_subscription_id' => $stripeSubscriptionId,
            ]);

            return;
        }

        // Desactivar la suscripción
        $subscription->setIsActive(false);
        $subscription->setPaymentStatus('cancelled');
        $subscription->setEndedAt(new \DateTime());

        // Use domain service to properly track the update
        $this->subscriptionDomainService->updateSubscription(
            $subscription,
            null, // No specific user for webhook context
            'webhook_cancellation'
        );

        $this->logger->info('Subscription deactivated after Stripe deletion', [
            'subscription_uuid' => $subscription->getUuidSubscription(),
            'stripe_subscription_id' => $stripeSubscriptionId,
            'client_uuid' => $subscription->getClient()->getUuidClient(),
        ]);
    }

    /**
     * Check if a client has active subscription (for access control).
     */
    public function hasActiveSubscription(string $clientUuid): bool
    {
        $client = $this->clientRepository->findByUuid($clientUuid);

        if (!$client) {
            return false;
        }

        $activeSubscriptions = $this->subscriptionRepository->findActiveByClient($client);

        return count($activeSubscriptions) > 0;
    }

    /**
     * Create a subscription from checkout session data using domain service.
     */
    private function createSubscriptionFromCheckoutSession(object $checkoutSession, string $stripeSubscriptionId): ?Subscription
    {
        try {
            // Try to find client by Stripe customer ID or email
            $client = null;

            // First try by Stripe customer ID
            if (isset($checkoutSession->customer)) {
                $client = $this->clientRepository->findByStripeCustomerId($checkoutSession->customer);
            }

            // If not found, try by customer email
            if (!$client && isset($checkoutSession->customer_email)) {
                $client = $this->clientRepository->findByCompanyEmail($checkoutSession->customer_email);
            }

            if (!$client) {
                $this->logger->error('Cannot create subscription: Client not found', [
                    'checkout_session_id' => $checkoutSession->id,
                    'stripe_customer_id' => $checkoutSession->customer ?? 'unknown',
                    'customer_email' => $checkoutSession->customer_email ?? 'unknown',
                ]);

                return null;
            }

            // Try to find subscription plan by the line items (price ID)
            $plan = null;
            if (isset($checkoutSession->display_items) && !empty($checkoutSession->display_items)) {
                foreach ($checkoutSession->display_items as $item) {
                    if (isset($item->price->id)) {
                        $plan = $this->subscriptionPlanRepository->findByStripePriceId($item->price->id);
                        if ($plan) {
                            break;
                        }
                    }
                }
            }

            if (!$plan) {
                $this->logger->error('Cannot create subscription: Subscription plan not found', [
                    'checkout_session_id' => $checkoutSession->id,
                    'display_items' => $checkoutSession->display_items ?? [],
                ]);

                return null;
            }

            // Use domain service to create the subscription without duplicating Stripe subscription
            return $this->subscriptionDomainService->createSubscriptionFromWebhook(
                $client,
                $plan,
                $stripeSubscriptionId,
                new \DateTime(), // started_at
                null // ended_at (open-ended)
            );
        } catch (\Throwable $e) {
            $this->logger->error('Error creating subscription from checkout session', [
                'checkout_session_id' => $checkoutSession->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Update subscription with data from checkout session.
     */
    private function updateSubscriptionFromCheckoutSession(Subscription $subscription, object $checkoutSession): void
    {
        // Activate the subscription and mark as paid
        $subscription->setIsActive(true);
        $subscription->setPaymentStatus('paid');

        // If it's a new subscription (no started_at date), set it
        if (!$subscription->getStartedAt()) {
            $subscription->setStartedAt(new \DateTime());
        }

        // Use domain service to properly track the update
        $this->subscriptionDomainService->updateSubscription(
            $subscription,
            null, // No specific user for webhook context
            'webhook_update'
        );

        // You could potentially set end date here if available in checkout session
        // For now, we'll leave it null for open-ended subscriptions
    }
}
