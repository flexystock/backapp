<?php

namespace App\Ttn\Application\InputPorts;

use App\Ttn\Application\DTO\RegisterAppTtnRequest;
use App\Ttn\Application\DTO\RegisterAppTtnResponse;
interface RegisterAppTtnUseCaseInterface
{
    public function execute(RegisterAppTtnRequest $request): RegisterAppTtnResponse;
}