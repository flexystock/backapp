<?php

namespace App\Service\Merma\Application\InputPorts;

use App\Service\Merma\Application\DTO\GetProductServiceHoursRequest;

interface GetProductServiceHoursUseCaseInterface
{
    public function execute(GetProductServiceHoursRequest $request): array;
}
