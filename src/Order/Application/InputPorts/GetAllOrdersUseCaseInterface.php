<?php

namespace App\Order\Application\InputPorts;

use App\Order\Application\DTO\GetAllOrdersRequest;
use App\Order\Application\DTO\GetAllOrdersResponse;

interface GetAllOrdersUseCaseInterface
{
    public function execute(GetAllOrdersRequest $request): GetAllOrdersResponse;
}
