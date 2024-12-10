<?php

// src/Product/Application/InputPorts/GetProductUseCaseInterface.php

namespace App\Product\Application\InputPorts;

use App\Product\Application\DTO\GetAllProductsRequest;
use App\Product\Application\DTO\GetProductResponse;

interface GetAllProductsUseCaseInterface
{
    /**
     * Ejecuta el caso de uso para obtener todos los productos de un cliente.
     */
    public function execute(GetAllProductsRequest $request): GetProductResponse;
}