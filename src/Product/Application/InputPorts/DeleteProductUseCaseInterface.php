<?php

namespace App\Product\Application\InputPorts;

use App\Entity\Main\User;
use App\Product\Application\DTO\DeleteProductRequest;
use App\Product\Application\DTO\DeleteProductResponse;

interface DeleteProductUseCaseInterface
{
    public function execute(DeleteProductRequest $request, User $user): DeleteProductResponse;
}
