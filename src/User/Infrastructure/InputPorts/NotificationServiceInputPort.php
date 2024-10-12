<?php

namespace App\User\Infrastructure\InputPorts;

use App\Entity\Main\User;
interface NotificationServiceInputPort
{
    public function sendEmailVerification(User $user): void;
}