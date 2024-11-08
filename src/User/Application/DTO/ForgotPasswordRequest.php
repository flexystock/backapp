<?php

namespace App\User\Application\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ForgotPasswordRequest
{
    #[Assert\NotBlank(message: "El email es obligatorio.")]
    #[Assert\Email(message: "El correo electrónico '{{ value }}' no es válido.")]
    public string $email;
}