<?php

namespace App\User\Application\DTO\Password;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordRequest
{
    #[Assert\NotBlank(message: 'EMAIL_REQUIRED')]
    #[Assert\Email(message: "INVALID_EMAIL")]
    public string $email;

    #[Assert\NotBlank(message: 'TOKEN_REQUIRED')]
    public string $token;

    #[Assert\NotBlank(message: 'PASSWORD_REQUIRED')]
    #[Assert\Length(min: 8, minMessage: 'INVALID_PASSWORD')]
    public string $newPassword;
}
