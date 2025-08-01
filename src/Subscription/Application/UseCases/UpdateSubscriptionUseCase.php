<?php

namespace App\Subscription\Application\UseCases;

use App\Subscription\Application\DTO\UpdateSubscriptionRequest;
use App\Subscription\Application\DTO\UpdateSubscriptionResponse;
use App\Subscription\Application\InputPorts\UpdateSubscriptionUseCaseInterface;
use App\Subscription\Application\OutputPorts\SubscriptionRepositoryInterface;
use App\Entity\Main\SubscriptionPlan;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class UpdateSubscriptionUseCase implements UpdateSubscriptionUseCaseInterface
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

    public function execute(UpdateSubscriptionRequest $request): UpdateSubscriptionResponse
    {
        try {
            $subscription = $this->subscriptionRepository->findByUuid($request->getUuidSubscription());
            if (!$subscription) {
                return new UpdateSubscriptionResponse(null, 'SUBSCRIPTION_NOT_FOUND', 404);
            }

            if (null !== $request->getPlanId()) {
                $plan = $this->entityManager->getReference(SubscriptionPlan::class, $request->getPlanId());
                $subscription->setPlan($plan);
            }
            if (null !== $request->getEndedAt()) {
                $subscription->setEndedAt($request->getEndedAt());
            }
            if (null !== $request->getIsActive()) {
                $subscription->setIsActive($request->getIsActive());
            }

            $subscription->setUpdatedAt(new \DateTime());
            $this->subscriptionRepository->save($subscription);

            $data = [
                'uuid' => $subscription->getUuidSubscription(),
            ];

            return new UpdateSubscriptionResponse($data, null, 200);
        } catch (\Throwable $e) {
            $this->logger->error('UpdateSubscriptionUseCase error', ['exception' => $e]);
            return new UpdateSubscriptionResponse(null, 'INTERNAL_ERROR', 500);
        }
    }
}
