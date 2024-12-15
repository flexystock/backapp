<?php

namespace App\Product\Application\InputPorts;

use App\Product\Application\DTO\DeleteProductRequest;
use App\Product\Application\DTO\DeleteProductResponse;

interface DeleteProductUseCaseInterface
{
    public function execute(DeleteProductRequest $request): DeleteProductResponse;
}
