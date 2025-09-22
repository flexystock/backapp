<?php

namespace App\Subscription\Application\UseCases;

use App\Entity\Main\Client;
use App\Entity\Main\Subscription;
use App\Entity\Main\SubscriptionPlan;
use App\Infrastructure\Services\PaymentGatewayService;
use App\Subscription\Application\DTO\CreateSubscriptionRequest;
use App\Subscription\Application\DTO\CreateSubscriptionResponse;
use App\Subscription\Application\InputPorts\CreateSubscriptionUseCaseInterface;
use App\Subscription\Application\OutputPorts\SubscriptionRepositoryInterface;
use App\Subscription\Application\Services\SubscriptionDomainService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class CreateSubscriptionUseCase implements CreateSubscriptionUseCaseInterface
{
    private SubscriptionRepositoryInterface $subscriptionRepository;
    private SubscriptionDomainService $subscriptionDomainService;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private PaymentGatewayService $paymentGateway;

    public function __construct(
        SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionDomainService $subscriptionDomainService,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        PaymentGatewayService $paymentGateway,
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->subscriptionDomainService = $subscriptionDomainService;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->paymentGateway = $paymentGateway;
    }

    public function execute(CreateSubscriptionRequest $request): CreateSubscriptionResponse
    {
        try {
            // Get entities from references
            $client = $this->entityManager->getReference(Client::class, $request->getClientUuid());
            $plan = $this->entityManager->getReference(SubscriptionPlan::class, $request->getPlanId());

            // Create subscription using domain service (without Stripe ID initially)
            $subscription = $this->subscriptionDomainService->createSubscription(
                $client,
                $plan,
                $request->getStartedAt(),
                $request->getEndedAt(),
                null, // No Stripe ID yet
                $request->getUuidUser(),
                'usecase'
            );

            // Create subscription in Stripe
            $result = $this->paymentGateway->createStripeSubscription($subscription, $plan, $client);

            // Update the subscription with the Stripe ID using domain service
            $subscription->setStripeSubscriptionId($result['subscription_id']);
            $this->subscriptionDomainService->updateSubscription(
                $subscription,
                $request->getUuidUser(),
                'stripe_integration'
            );

            $this->logger->info('Subscription created successfully via UseCase', [
                'uuid_subscription' => $subscription->getUuidSubscription(),
                'stripe_subscription_id' => $result['subscription_id'],
            ]);

            $data = [
                'uuid' => $subscription->getUuidSubscription(),
                'stripe_subscription_id' => $result['subscription_id'],
                'client_secret' => $result['client_secret'],
            ];

            return new CreateSubscriptionResponse($data, null, 201);
        } catch (\Throwable $e) {
            $this->logger->error('CreateSubscriptionUseCase error', ['exception' => $e->getMessage()]);

            return new CreateSubscriptionResponse(null, 'INTERNAL_ERROR', 500);
        }
    }
}
