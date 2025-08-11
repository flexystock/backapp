<?php

namespace App\Subscription\Application\UseCases;

use App\Subscription\Application\DTO\DeleteSubscriptionRequest;
use App\Subscription\Application\DTO\DeleteSubscriptionResponse;
use App\Subscription\Application\InputPorts\DeleteSubscriptionUseCaseInterface;
use App\Subscription\Application\OutputPorts\SubscriptionRepositoryInterface;
use Psr\Log\LoggerInterface;

class DeleteSubscriptionUseCase implements DeleteSubscriptionUseCaseInterface
{
    private SubscriptionRepositoryInterface $subscriptionRepository;
    private LoggerInterface $logger;

    public function __construct(SubscriptionRepositoryInterface $subscriptionRepository, LoggerInterface $logger)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->logger = $logger;
    }

    public function execute(DeleteSubscriptionRequest $request): DeleteSubscriptionResponse
    {
        try {
            $subscription = $this->subscriptionRepository->findByUuid($request->getUuidSubscription());
            if (!$subscription) {
                return new DeleteSubscriptionResponse(null, 'SUBSCRIPTION_NOT_FOUND', 404);
            }

            $this->subscriptionRepository->remove($subscription);

            return new DeleteSubscriptionResponse('SUBSCRIPTION_DELETED', null, 200);
        } catch (\Throwable $e) {
            $this->logger->error('DeleteSubscriptionUseCase error', ['exception' => $e]);

            return new DeleteSubscriptionResponse(null, 'INTERNAL_ERROR', 500);
        }
    }
}
