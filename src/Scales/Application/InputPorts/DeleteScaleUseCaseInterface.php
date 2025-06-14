<?php

namespace App\Scales\Application\InputPorts;

use App\Entity\Main\User;
use App\Scales\Application\DTO\DeleteScaleRequest;
use App\Scales\Application\DTO\DeleteScaleResponse;

interface DeleteScaleUseCaseInterface
{
    public function execute(DeleteScaleRequest $request, User $user): DeleteScaleResponse;
}
