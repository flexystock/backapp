<?php

namespace App\Supplier\Application\InputPorts;

use App\Supplier\Application\DTO\GetAllSuppliersRequest;
use App\Supplier\Application\DTO\GetAllSuppliersResponse;

interface GetAllSuppliersUseCaseInterface
{
    public function execute(GetAllSuppliersRequest $request): GetAllSuppliersResponse;
}
