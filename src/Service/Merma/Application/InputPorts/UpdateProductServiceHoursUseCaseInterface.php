<?php

namespace App\Service\Merma\Application\InputPorts;

use App\Service\Merma\Application\DTO\UpdateProductServiceHoursRequest;

interface UpdateProductServiceHoursUseCaseInterface
{
    public function execute(UpdateProductServiceHoursRequest $request): void;
}
