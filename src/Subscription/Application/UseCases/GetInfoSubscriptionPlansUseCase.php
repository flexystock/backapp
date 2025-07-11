<?php

namespace App\Subscription\Application\UseCases;
use App\Entity\Main\SubscriptionPlan;
use Psr\Log\LoggerInterface;
use App\Subscription\Application\DTO\GetInfoSubscriptionPlansRequest;
use App\Subscription\Application\DTO\GetInfoSubscriptionPlansResponse;
use App\Subscription\Application\InputPorts\GetInfoSubscriptionPlansUseCaseInterface;
use App\Subscription\Application\OutputPorts\SubscriptionPlanRepositoryInterface;

class GetInfoSubscriptionPlansUseCase implements GetInfoSubscriptionPlansUseCaseInterface
{
    private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository;
    private LoggerInterface $logger;

    public function __construct(
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
        LoggerInterface $logger
    ) {
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
        $this->logger = $logger;
    }

    public function execute(): GetInfoSubscriptionPlansResponse
    {
        $this->logger->info('Fetching subscription plans information');

        // Fetch all subscription plans from the repository
        $subscriptionPlans = $this->subscriptionPlanRepository->findAll();

        // Map the subscription plans to a response DTO
        $plansData = [];
        foreach ($subscriptionPlans as $plan) {
            if (!$plan instanceof SubscriptionPlan) {
                continue;
            }
            $plansData[] = [
                'id' => $plan->getId(),
                'name' => $plan->getName(),
                'description' => $plan->getDescription(),
                'price' => $plan->getPrice(),
                'maxScales' => $plan->getMaxScales(),
            ];
        }

        return new GetInfoSubscriptionPlansResponse($plansData, null, 201);
    }
}