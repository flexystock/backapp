<?php

namespace App\User\Application\InputPorts;

use App\User\Application\DTO\ForgotPasswordRequest;

interface RequestPasswordResetInterface
{
    public function requestPasswordReset(ForgotPasswordRequest $request): void;
}