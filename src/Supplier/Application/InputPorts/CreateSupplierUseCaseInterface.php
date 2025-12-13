<?php

namespace App\Supplier\Application\InputPorts;

use App\Supplier\Application\DTO\CreateSupplierRequest;
use App\Supplier\Application\DTO\CreateSupplierResponse;

interface CreateSupplierUseCaseInterface
{
    public function execute(CreateSupplierRequest $request): CreateSupplierResponse;
}
