<?php

namespace App\User\Application\InputPorts;

use App\User\Application\DTO\Password\ResetPasswordRequest;

interface ResetPasswordInterface
{
    public function resetPassword(ResetPasswordRequest $request): void;
}
