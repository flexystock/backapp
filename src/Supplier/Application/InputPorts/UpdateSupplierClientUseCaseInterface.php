<?php

namespace App\Supplier\Application\InputPorts;

use App\Supplier\Application\DTO\UpdateSupplierClientRequest;
use App\Supplier\Application\DTO\UpdateSupplierClientResponse;

interface UpdateSupplierClientUseCaseInterface
{
    public function execute(UpdateSupplierClientRequest $request): UpdateSupplierClientResponse;
}
