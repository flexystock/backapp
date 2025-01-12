<?php

namespace App\Ttn\Application\InputPorts;

use App\Ttn\Application\DTO\TtnUplinkRequest;

interface HandleTtnUplinkUseCaseInterface
{
    public function execute(TtnUplinkRequest $request): void;
}