<?php

namespace App\Service\Merma\Application\OutputPorts;

use App\Entity\Client\Scales;

interface GetScalesByProductRepositoryInterface
{
    /**
     * Returns all scales associated with the given product.
     *
     * @return Scales[]
     */
    public function findAllByProductId(int $productId): array;
}
