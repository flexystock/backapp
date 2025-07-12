<?php

namespace App\Subscription\Application\UseCases;

use App\Subscription\Application\DTO\DeleteSubscriptionPlanRequest;
use App\Subscription\Application\DTO\DeleteSubscriptionPlanResponse;
use App\Subscription\Application\InputPorts\DeleteSubscriptionPlanUseCaseInterface;
use App\Subscription\Application\OutputPorts\SubscriptionPlanRepositoryInterface;
use Psr\Log\LoggerInterface;

class DeleteSubscriptionPlanUseCase implements DeleteSubscriptionPlanUseCaseInterface
{
    private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository;
    private LoggerInterface $logger;

    public function __construct(SubscriptionPlanRepositoryInterface $subscriptionPlanRepository, LoggerInterface $logger)
    {
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
        $this->logger = $logger;
    }

    public function execute(DeleteSubscriptionPlanRequest $request): DeleteSubscriptionPlanResponse
    {
        try {
            $plan = $this->subscriptionPlanRepository->findByUuid($request->getId());
            if (!$plan) {
                return new DeleteSubscriptionPlanResponse(null, 'PLAN_NOT_FOUND', 404);
            }

            $this->subscriptionPlanRepository->remove($plan);

            return new DeleteSubscriptionPlanResponse('PLAN_DELETED_SUCCESSFULLY', null, 200);
        } catch (\Exception $e) {
            $this->logger->error('DeleteSubscriptionPlanUseCase: Error', ['exception' => $e]);
            return new DeleteSubscriptionPlanResponse(null, 'Internal Server Error', 500);
        }
    }
}
