<?php

namespace App\Service\Merma\Application\InputPorts;

use App\Service\Merma\Application\DTO\ConfirmAnomalyRequest;

interface ConfirmAnomalyUseCaseInterface
{
    public function execute(ConfirmAnomalyRequest $request): void;
}
