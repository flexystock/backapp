<?php

namespace App\Product\Application\InputPorts;

use App\Product\Application\DTO\CreateProductRequest;
use App\Product\Application\DTO\CreateProductResponse;

interface CreateProductUseCaseInterface
{
    public function execute(CreateProductRequest $request): CreateProductResponse;
}
