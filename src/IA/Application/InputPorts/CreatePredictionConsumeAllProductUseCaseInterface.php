<?php

namespace App\IA\Application\InputPorts;

use App\IA\Application\DTO\CreatePredictionConsumeAllProductRequest;
use App\IA\Application\DTO\CreatePredictionConsumeAllProductResponse;

interface CreatePredictionConsumeAllProductUseCaseInterface
{
    public function execute(CreatePredictionConsumeAllProductRequest $request): CreatePredictionConsumeAllProductResponse;
}
