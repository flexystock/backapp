<?php

namespace App\Service\Merma\Application\InputPorts;

use App\Service\Merma\Application\DTO\UpdateMermaConfigRequest;

interface UpdateMermaConfigUseCaseInterface
{
    public function execute(UpdateMermaConfigRequest $request): array;
}
