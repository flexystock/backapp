<?php

namespace App\Ttn\Application\UseCases;

use App\Ttn\Application\DTO\RegisterDeviceRequest;
use App\Ttn\Application\DTO\RegisterDeviceResponse;
use App\Ttn\Application\InputPorts\RegisterDeviceUseCaseInterface;
use App\Ttn\Application\OutputPorts\TtnServiceInterface;

class RegisterDeviceUseCase implements RegisterDeviceUseCaseInterface
{
    private TtnServiceInterface $ttnService;

    public function __construct(TtnServiceInterface $ttnService)
    {
        $this->ttnService = $ttnService;
    }

    public function execute(RegisterDeviceRequest $request): RegisterDeviceResponse
    {
        return $this->ttnService->registerDevice($request);
    }
}