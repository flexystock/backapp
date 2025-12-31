<?php

namespace App\Supplier\Application\InputPorts;

use App\Supplier\Application\DTO\GetAllSuppliersClientRequest;
use App\Supplier\Application\DTO\GetAllSuppliersClientResponse;

interface GetAllSuppliersClientUseCaseInterface
{
    public function execute(GetAllSuppliersClientRequest $request): GetAllSuppliersClientResponse;
}
