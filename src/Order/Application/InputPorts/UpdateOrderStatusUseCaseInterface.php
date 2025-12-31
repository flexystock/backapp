<?php

namespace App\Order\Application\InputPorts;

use App\Order\Application\DTO\UpdateOrderStatusRequest;
use App\Order\Application\DTO\UpdateOrderStatusResponse;

interface UpdateOrderStatusUseCaseInterface
{
    public function execute(UpdateOrderStatusRequest $request): UpdateOrderStatusResponse;
}
