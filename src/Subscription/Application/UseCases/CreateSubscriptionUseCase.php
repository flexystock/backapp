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
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

class CreateSubscriptionUseCase implements CreateSubscriptionUseCaseInterface
{
    private SubscriptionRepositoryInterface $subscriptionRepository;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;
    private PaymentGatewayService $paymentGateway;

    public function __construct(
        SubscriptionRepositoryInterface $subscriptionRepository,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        PaymentGatewayService $paymentGateway,
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->paymentGateway = $paymentGateway;
    }

    public function execute(CreateSubscriptionRequest $request): CreateSubscriptionResponse
    {
        try {
            $subscription = new Subscription();
            $subscription->setUuidSubscription(Uuid::v4()->toRfc4122());
            $client = $this->entityManager->getReference(Client::class, $request->getClientUuid());
            $plan = $this->entityManager->getReference(SubscriptionPlan::class, $request->getPlanId());
            $subscription->setClient($client);
            $subscription->setPlan($plan);
            $subscription->setStartedAt($request->getStartedAt());
            $subscription->setEndedAt($request->getEndedAt());
            $subscription->setIsActive(true);
            $subscription->setCreatedAt(new \DateTime());
            $subscription->setUuidUserCreation($request->getUuidUser());

            $this->subscriptionRepository->save($subscription);

            // Aquí NO trabajas directamente con Stripe
            $result = $this->paymentGateway->createStripeSubscription($subscription, $plan, $client);

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
