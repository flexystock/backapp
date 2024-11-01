<?php

namespace App\User\Infrastructure\OutputPorts;
use App\Entity\Main\User;
interface NotificationServiceInterface
{
    public function sendEmailVerificationToUser(User $user): void;

    public function sendEmailToBack(User $user): void;

    public function sendPasswordResetEmail(User $user, $token):void;
}