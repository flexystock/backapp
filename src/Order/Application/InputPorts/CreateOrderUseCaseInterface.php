<?php

namespace App\Order\Application\InputPorts;

use App\Order\Application\DTO\CreateOrderRequest;
use App\Order\Application\DTO\CreateOrderResponse;

interface CreateOrderUseCaseInterface
{
    public function execute(CreateOrderRequest $request): CreateOrderResponse;
}
