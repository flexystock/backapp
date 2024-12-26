<?php

namespace App\Ttn\Application\InputPorts;

use App\Ttn\Application\DTO\RegisterTtnAppRequest;
use App\Ttn\Application\DTO\RegisterTtnAppResponse;
interface RegisterTtnAppUseCaseInterface
{
    public function execute(RegisterTtnAppRequest $request): RegisterTtnAppResponse;
}