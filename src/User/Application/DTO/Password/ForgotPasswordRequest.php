<?php

namespace App\User\Application\DTO\Password;

use Symfony\Component\Validator\Constraints as Assert;

class ForgotPasswordRequest
{
    #[Assert\NotBlank(message: 'EMAIL_REQUIRED')]
    #[Assert\Email(message: 'INVALID_EMAIL')]
    public string $email;
}
