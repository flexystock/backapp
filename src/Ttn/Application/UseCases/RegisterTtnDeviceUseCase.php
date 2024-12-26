<?php

namespace App\Ttn\Application\UseCases;

use App\Ttn\Application\DTO\RegisterTtnDeviceRequest;
use App\Ttn\Application\DTO\RegisterTtnDeviceResponse;
use App\Ttn\Application\InputPorts\RegisterTtnDeviceUseCaseInterface;
use App\Ttn\Application\OutputPorts\TtnServiceInterface;

class RegisterTtnDeviceUseCase implements RegisterTtnDeviceUseCaseInterface
{
    private TtnServiceInterface $ttnService;

    public function __construct(TtnServiceInterface $ttnService)
    {
        $this->ttnService = $ttnService;
    }

    public function execute(RegisterTtnDeviceRequest $request): RegisterTtnDeviceResponse
    {
        return $this->ttnService->registerDevice($request);
    }
}