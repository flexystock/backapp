<?php

namespace App\IA\Application\InputPorts;

use App\IA\Application\DTO\CreatePredictionConsumeProductRequest;
use App\IA\Application\DTO\CreatePredictionConsumeProductResponse;

interface CreatePredictionConsumeProductUseCaseInterface
{
    public function execute(CreatePredictionConsumeProductRequest $request): CreatePredictionConsumeProductResponse;
}
