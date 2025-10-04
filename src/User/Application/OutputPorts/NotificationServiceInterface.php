<?php

namespace App\User\Application\OutputPorts;

use App\Entity\Main\User;

interface NotificationServiceInterface
{
    public function sendEmailVerificationToUser(User $user): void;

    public function sendEmailVerificationCreatedClientToUser(User $user): void;

    public function sendEmailToBack(User $user): void;

    public function sendEmailAccountPendingVerificationToUser(User $user): void;

    public function sendPasswordResetEmail(User $user, $token): void;

    public function sendSuccesfullPasswordResetEmail(User $user): void;

    public function sendEmailAccountVerifiedToUser(User $user): void;

    public function sendNewUserInvitationEmail(User $user, string $forgotPasswordUrl): void;
}
