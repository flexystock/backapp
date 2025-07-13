<?php

namespace App\Subscription\Application\UseCases;

use App\Subscription\Application\DTO\GetInfoSubscriptionRequest;
use App\Subscription\Application\DTO\GetInfoSubscriptionResponse;
use App\Subscription\Application\InputPorts\GetInfoSubscriptionUseCaseInterface;
use App\Subscription\Application\OutputPorts\SubscriptionRepositoryInterface;
use Psr\Log\LoggerInterface;
use App\Entity\Main\Subscription;

class GetInfoSubscriptionUseCase implements GetInfoSubscriptionUseCaseInterface
{
    private SubscriptionRepositoryInterface $subscriptionRepository;
    private LoggerInterface $logger;

    public function __construct(SubscriptionRepositoryInterface $subscriptionRepository, LoggerInterface $logger)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->logger = $logger;
    }

    public function execute(GetInfoSubscriptionRequest $request): GetInfoSubscriptionResponse
    {
        try {
            if ($request->getUuidSubscription()) {
                $subscription = $this->subscriptionRepository->findByUuid($request->getUuidSubscription());
                if (!$subscription) {
                    return new GetInfoSubscriptionResponse(null, 'SUBSCRIPTION_NOT_FOUND', 404);
                }
                $data = [
                    'uuid' => $subscription->getUuidSubscription(),
                    'clientUuid' => $subscription->getClient()->getUuidClient(),
                    'planId' => $subscription->getPlan()->getId(),
                ];
                return new GetInfoSubscriptionResponse($data, null, 200);
            }

            $subscriptions = $this->subscriptionRepository->findAll();
            $data = [];
            foreach ($subscriptions as $sub) {
                if (!$sub instanceof Subscription) {
                    continue;
                }
                $data[] = [
                    'uuid' => $sub->getUuidSubscription(),
                    'clientUuid' => $sub->getClient()->getUuidClient(),
                    'planId' => $sub->getPlan()->getId(),
                ];
            }
            return new GetInfoSubscriptionResponse($data, null, 200);
        } catch (\Throwable $e) {
            $this->logger->error('GetInfoSubscriptionUseCase error', ['exception' => $e]);
            return new GetInfoSubscriptionResponse(null, 'INTERNAL_ERROR', 500);
        }
    }
}
