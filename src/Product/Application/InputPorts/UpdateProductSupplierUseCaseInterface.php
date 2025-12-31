<?php

namespace App\Product\Application\InputPorts;

use App\Product\Application\DTO\UpdateProductSupplierRequest;
use App\Product\Application\DTO\UpdateProductSupplierResponse;

interface UpdateProductSupplierUseCaseInterface
{
    public function execute(UpdateProductSupplierRequest $request): UpdateProductSupplierResponse;
}
