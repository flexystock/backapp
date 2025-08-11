<?php

namespace App\User\Application\InputPorts\Auth;

use App\Entity\Main\User;

interface ResendEmailVerificationTokenInterface
{
    public function resendEmailVerificationToken(User $user, string $token): bool;
}
