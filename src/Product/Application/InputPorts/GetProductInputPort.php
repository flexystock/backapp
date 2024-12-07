<?php
namespace App\Product\Application\InputPorts;

use App\Product\Application\DTO\ProductDTO;

interface GetProductInputPort
{
    public function execute(string $productUuid): ProductDTO;
}
