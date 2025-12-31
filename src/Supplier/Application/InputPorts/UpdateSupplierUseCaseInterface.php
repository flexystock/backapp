<?php

namespace App\Supplier\Application\InputPorts;

use App\Supplier\Application\DTO\UpdateSupplierRequest;
use App\Supplier\Application\DTO\UpdateSupplierResponse;

interface UpdateSupplierUseCaseInterface
{
    public function execute(UpdateSupplierRequest $request): UpdateSupplierResponse;
}
