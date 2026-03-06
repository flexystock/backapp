<?php

namespace App\Service\Merma\Application\InputPorts;

use App\Service\Merma\Application\DTO\GetMermaSummaryRequest;
use App\Service\Merma\Application\DTO\MermaSummaryDTO;

interface GetMermaSummaryUseCaseInterface
{
    public function execute(GetMermaSummaryRequest $request): MermaSummaryDTO;
}
