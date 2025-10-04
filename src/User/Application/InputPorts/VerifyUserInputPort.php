<?php

declare(strict_types=1);

namespace App\User\Application\InputPorts;

use App\User\Application\DTO\Management\VerifyUserRequest;

interface VerifyUserInputPort
{
    public function verify(VerifyUserRequest $request): void;
}
