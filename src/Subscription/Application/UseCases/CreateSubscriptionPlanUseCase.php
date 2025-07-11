<?php

namespace App\Subscription\Application\UseCases;

use App\Subscription\Application\InputPorts\CreateSubscriptionPlanUseCaseInterface;
use App\Subscription\Application\DTO\CreateSubscriptionPlanRequest;
use App\Subscription\Application\DTO\CreateSubscriptionPlanResponse;
use App\Entity\Main\SubscriptionPlan;
use App\Subscription\Application\OutputPorts\SubscriptionPlanRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateSubscriptionPlanUseCase implements CreateSubscriptionPlanUseCaseInterface
{
    private SubscriptionPlanRepositoryInterface $planRepository;
    private LoggerInterface $logger;

    public function __construct(SubscriptionPlanRepositoryInterface $planRepository, LoggerInterface $logger)
    {
        $this->planRepository = $planRepository;
        $this->logger = $logger;
    }

    public function execute(CreateSubscriptionPlanRequest $request): CreateSubscriptionPlanResponse
    {
        $this->logger->info('Creating subscription plan', [
            'name' => $request->getName(),
            'price' => $request->getPrice(),
        ]);

        // Validate the request data
        if (empty($request->getName())) {
            throw new \InvalidArgumentException('Subscription plan name cannot be empty.');
        }

        if ($request->getPrice() <= 0) {
            throw new \InvalidArgumentException('Subscription plan price must be greater than zero.');
        }
        if (empty($request->getMaxScales())) {
            throw new \InvalidArgumentException('Subscription plan max scales cannot be empty.');
        }

        // Check if plan with the same name already exists
        if (null !== $this->planRepository->findByName($request->getName())) {
            throw new \RuntimeException('PLAN_ALREADY_EXISTS');
        }

        // Create the subscription plan entity
        $subscriptionPlan = new SubscriptionPlan();
        $subscriptionPlan->setName($request->getName());
        $subscriptionPlan->setDescription($request->getDescription());
        $subscriptionPlan->setPrice($request->getPrice());
        $subscriptionPlan->setMaxScales($request->getMaxScales());

        // Save the subscription plan using the repository
        $this->planRepository->save($subscriptionPlan);

        $planData = [
            'id' => $subscriptionPlan->getId(),
            'name' => $subscriptionPlan->getName(),
        ];

        return new CreateSubscriptionPlanResponse($planData, null, 201);
    }

}
