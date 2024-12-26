<?php

namespace App\Ttn\Application\UseCases;

use App\Ttn\Application\DTO\RegisterTtnAppRequest;
use App\Ttn\Application\DTO\RegisterTtnAppResponse;
use App\Ttn\Application\InputPorts\RegisterTtnAppUseCaseInterface;
use App\Ttn\Application\OutputPorts\TtnServiceInterface;

class RegisterTtnAppUseCase implements RegisterTtnAppUseCaseInterface
{
    private TtnServiceInterface $ttnService;

    public function __construct(TtnServiceInterface $ttnService)
    {
        $this->ttnService = $ttnService;
    }

    public function execute(RegisterTtnAppRequest $request): RegisterTtnAppResponse
    {
        return $this->ttnService->registerApp($request);
    }
}
