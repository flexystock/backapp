<?php

namespace App\Service\Merma\Application\InputPorts;

use App\Service\Merma\Application\DTO\DiscardAnomalyRequest;

interface DiscardAnomalyUseCaseInterface
{
    public function execute(DiscardAnomalyRequest $request): void;
}
