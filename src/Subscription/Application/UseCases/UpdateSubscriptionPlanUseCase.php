<?php

namespace App\Subscription\Application\UseCases;

use App\Subscription\Application\DTO\UpdateSubscriptionPlanRequest;
use App\Subscription\Application\DTO\UpdateSubcriptionPlanResponse;
use App\Subscription\Application\InputPorts\UpdateSubscriptionPlanUseCaseInterface;
use App\Subscription\Application\OutputPorts\SubscriptionPlanRepositoryInterface;
use Psr\Log\LoggerInterface;

class UpdateSubscriptionPlanUseCase implements UpdateSubscriptionPlanUseCaseInterface
{
    private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository;
    private LoggerInterface $logger;

    public function __construct(SubscriptionPlanRepositoryInterface $subscriptionPlanRepository, LoggerInterface $logger)
    {
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
        $this->logger = $logger;
    }

    public function execute(UpdateSubscriptionPlanRequest $request): UpdateSubcriptionPlanResponse
    {
        try {
            $plan = $this->subscriptionPlanRepository->findByUuid($request->getId());
            if (!$plan) {
                return new UpdateSubcriptionPlanResponse(null, 'PLAN_NOT_FOUND', 404);
            }

            if (null !== $request->getName()) {
                $plan->setName($request->getName());
            }
            if (null !== $request->getDescription()) {
                $plan->setDescription($request->getDescription());
            }
            if (null !== $request->getPrice()) {
                $plan->setPrice($request->getPrice());
            }
            if (null !== $request->getMaxScales()) {
                $plan->setMaxScales($request->getMaxScales());
            }

            $plan->setUuidUserModification($request->getUuidUserModification());
            $plan->setDatehourModification($request->getDatehourModification());

            $this->subscriptionPlanRepository->save($plan);

            $data = [
                'id' => $plan->getId(),
                'name' => $plan->getName(),
            ];

            return new UpdateSubcriptionPlanResponse($data, null, 200);
        } catch (\Exception $e) {
            $this->logger->error('UpdateSubscriptionPlanUseCase: Error', ['exception' => $e]);
            return new UpdateSubcriptionPlanResponse(null, 'Internal Server Error', 500);
        }
    }
}
