<?php

namespace App\Service\Merma\Application\DTO;

class GetScalesByProductResponse
{
    /** @param array<int, array<string, mixed>> $scales */
    public function __construct(
        public readonly array $scales,
    ) {}
}
