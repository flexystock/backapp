<?php

// src/Product/Application/InputPorts/GetInfoToDashboardMainUseCaseInterface.php

namespace App\Product\Application\InputPorts;

use App\Product\Application\DTO\GetInfoToDashboardMainRequest;
use App\Product\Application\DTO\GetInfoToDashboardMainResponse;

interface GetInfoToDashboardMainUseCaseInterface
{
    /**
     * Ejecuta el caso de uso para obtener todos los productos de un cliente.
     */
    public function execute(GetInfoToDashboardMainRequest $request): GetInfoToDashboardMainResponse;
}