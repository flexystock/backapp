<?php

namespace App\User\Application\DTO\Password;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordRequest
{
    #[Assert\NotBlank(message: "El email es obligatorio.")]
    #[Assert\Email(message: "El correo electrónico '{{ value }}' no es válido.")]
    public string $email;

    #[Assert\NotBlank(message: "El código es obligatorio.")]
    public string $token;

    #[Assert\NotBlank(message: "La nueva contraseña es obligatoria.")]
    #[Assert\Length(min: 8, minMessage: "La contraseña debe tener al menos {{ limit }} caracteres.")]
    public string $newPassword;
}