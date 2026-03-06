<?php

namespace App\Service\Merma\Application\InputPorts;

use App\Service\Merma\Application\DTO\GetMermaConfigRequest;

interface GetMermaConfigUseCaseInterface
{
    public function execute(GetMermaConfigRequest $request): array;
}
