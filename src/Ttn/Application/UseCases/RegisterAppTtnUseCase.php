<?php

namespace App\Ttn\Application\UseCases;

use App\Ttn\Application\DTO\RegisterAppTtnRequest;
use App\Ttn\Application\DTO\RegisterAppTtnResponse;
use App\Ttn\Application\InputPorts\RegisterAppTtnUseCaseInterface;
use App\Ttn\Application\OutputPorts\TtnServiceInterface;

class RegisterAppTtnUseCase implements RegisterAppTtnUseCaseInterface
{
    private TtnServiceInterface $ttnService;

    public function __construct(TtnServiceInterface $ttnService)
    {
        $this->ttnService = $ttnService;
    }

    public function execute(RegisterAppTtnRequest $request): RegisterAppTtnResponse
    {
        return $this->ttnService->registerApp($request);
    }
}
