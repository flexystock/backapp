<?php

namespace App\Product\Application\InputPorts;

use App\Product\Application\DTO\UpdateProductRequest;
use App\Product\Application\DTO\UpdateProductResponse;

interface UpdateProductUseCaseInterface
{
    public function execute(UpdateProductRequest $request): UpdateProductResponse;
}
