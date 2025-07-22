<?php

namespace App\Stripe\Application\UseCases;

use App\Stripe\Application\DTO\SetupIntentRequest;
use App\Stripe\Application\DTO\SetupIntentResponse;
use App\Stripe\Application\InputPorts\SetupIntentUseCaseInterface;
use App\Stripe\Application\OutputPorts\SetupIntentRepositoryInterface;

class SetupIntentUseCase implements SetupIntentUseCaseInterface
{
    public function __construct(private SetupIntentRepositoryInterface $repository)
    {
    }

    public function execute(SetupIntentRequest $request): SetupIntentResponse
    {
        $clientSecret = $this->repository->createSetupIntent($request->uuidClient);
        return new SetupIntentResponse($clientSecret);
    }
}
