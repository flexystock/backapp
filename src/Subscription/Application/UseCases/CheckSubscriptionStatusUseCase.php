<?php

namespace App\Subscription\Application\UseCases;

use App\Subscription\Application\DTO\CheckSubscriptionStatusRequest;
use App\Subscription\Application\DTO\CheckSubscriptionStatusResponse;
use App\Subscription\Application\InputPorts\CheckSubscriptionStatusUseCaseInterface;
use App\Subscription\Application\OutputPorts\SubscriptionRepositoryInterface;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use Psr\Log\LoggerInterface;

class CheckSubscriptionStatusUseCase implements CheckSubscriptionStatusUseCaseInterface
{
    private SubscriptionRepositoryInterface $subscriptionRepository;
    private ClientRepositoryInterface $clientRepository;
    private LoggerInterface $logger;

    public function __construct(
        SubscriptionRepositoryInterface $subscriptionRepository,
        ClientRepositoryInterface $clientRepository,
        LoggerInterface $logger
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->clientRepository = $clientRepository;
        $this->logger = $logger;
    }

    public function execute(CheckSubscriptionStatusRequest $request): CheckSubscriptionStatusResponse
    {
        try {
            // Check by specific subscription UUID
            if ($request->getSubscriptionUuid()) {
                return $this->checkSpecificSubscription($request->getSubscriptionUuid());
            }

            // Check by client UUID
            if ($request->getClientUuid()) {
                return $this->checkClientSubscriptions($request->getClientUuid());
            }

            return new CheckSubscriptionStatusResponse(null, 'MISSING_PARAMETERS', 400);

        } catch (\Throwable $e) {
            $this->logger->error('CheckSubscriptionStatusUseCase error', [
                'exception' => $e->getMessage(),
                'client_uuid' => $request->getClientUuid(),
                'subscription_uuid' => $request->getSubscriptionUuid()
            ]);

            return new CheckSubscriptionStatusResponse(null, 'INTERNAL_ERROR', 500);
        }
    }

    private function checkSpecificSubscription(string $subscriptionUuid): CheckSubscriptionStatusResponse
    {
        $subscription = $this->subscriptionRepository->findByUuid($subscriptionUuid);

        if (!$subscription) {
            return new CheckSubscriptionStatusResponse(null, 'SUBSCRIPTION_NOT_FOUND', 404);
        }

        // Check if this specific subscription is currently active
        $client = $subscription->getClient();
        $activeSubscriptions = $this->subscriptionRepository->findActiveByClient($client);
        $isActive = false;

        foreach ($activeSubscriptions as $activeSub) {
            if ($activeSub->getUuidSubscription() === $subscriptionUuid) {
                $isActive = true;
                break;
            }
        }

        $data = [
            'subscription_uuid' => $subscription->getUuidSubscription(),
            'client_uuid' => $subscription->getClient()->getUuidClient(),
            'is_active' => $isActive,
            'payment_status' => $subscription->getPaymentStatus(),
            'started_at' => $subscription->getStartedAt()?->format('Y-m-d H:i:s'),
            'ended_at' => $subscription->getEndedAt()?->format('Y-m-d H:i:s'),
            'plan_name' => $subscription->getPlan()->getName(),
            'stripe_subscription_id' => $subscription->getStripeSubscriptionId()
        ];

        return new CheckSubscriptionStatusResponse($data, null, 200);
    }

    private function checkClientSubscriptions(string $clientUuid): CheckSubscriptionStatusResponse
    {
        $client = $this->clientRepository->findByUuid($clientUuid);

        if (!$client) {
            return new CheckSubscriptionStatusResponse(null, 'CLIENT_NOT_FOUND', 404);
        }

        $activeSubscriptions = $this->subscriptionRepository->findActiveByClient($client);
        $hasActiveSubscription = count($activeSubscriptions) > 0;

        $subscriptionDetails = [];
        foreach ($activeSubscriptions as $subscription) {
            $subscriptionDetails[] = [
                'subscription_uuid' => $subscription->getUuidSubscription(),
                'payment_status' => $subscription->getPaymentStatus(),
                'started_at' => $subscription->getStartedAt()?->format('Y-m-d H:i:s'),
                'ended_at' => $subscription->getEndedAt()?->format('Y-m-d H:i:s'),
                'plan_name' => $subscription->getPlan()->getName(),
                'stripe_subscription_id' => $subscription->getStripeSubscriptionId()
            ];
        }

        $data = [
            'client_uuid' => $clientUuid,
            'has_active_subscription' => $hasActiveSubscription,
            'active_subscriptions_count' => count($activeSubscriptions),
            'active_subscriptions' => $subscriptionDetails
        ];

        return new CheckSubscriptionStatusResponse($data, null, 200);
    }
}