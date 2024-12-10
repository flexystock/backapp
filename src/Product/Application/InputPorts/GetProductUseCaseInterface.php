<?php

// src/Product/Application/InputPorts/GetProductUseCaseInterface.php

namespace App\Product\Application\InputPorts;

use App\Product\Application\DTO\GetProductRequest;
use App\Product\Application\DTO\GetProductResponse;

interface GetProductUseCaseInterface
{
    /**
     * Ejecuta el caso de uso para obtener un producto.
     */
    public function execute(GetProductRequest $request): GetProductResponse;
}
