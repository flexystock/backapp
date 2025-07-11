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
    private CreateSubscriptionPlanUseCaseInterface $createSubscriptionPlanUseCase;
    private LoggerInterface $logger;

    public function __construct(
        CreateSubscriptionPlanUseCaseInterface $createSubscriptionPlanUseCase,
        LoggerInterface $logger
    ) {
        $this->createSubscriptionPlanUseCase = $createSubscriptionPlanUseCase;
        $this->logger = $logger;
    }

    public function execute(CreateSubscriptionPlanRequest $Request): SubscriptionPlan
    {
        $this->logger->info('Creating subscription plan', [
            'name' => $Request->getName(),
            'price' => $Request->getPrice(),
        ]);

        // Validate the request data
        if (empty($Request->getName())) {
            throw new \InvalidArgumentException('Subscription plan name cannot be empty.');
        }

        if ($Request->getPrice() <= 0) {
            throw new \InvalidArgumentException('Subscription plan price must be greater than zero.');
        }
        if (empty($Request->getMaxScales())) {
            throw new \InvalidArgumentException('Subscription plan max scales cannot be empty.');
        }

        // Create the subscription plan entity
        $subscriptionPlan = new SubscriptionPlan();
        $subscriptionPlan->setName($Request->getName());
        $subscriptionPlan->setDescription($Request->getDescription());
        $subscriptionPlan->setPrice($Request->getPrice());
        $subscriptionPlan->setMaxScales($Request->getMaxScales());

        // Save the subscription plan using the repository
        $this->createSubscriptionPlanUseCase->save($subscriptionPlan);

        return $subscriptionPlan;
    }

}
