<?php

namespace App\User\Application;

use App\User\Application\DTO\ResetPasswordRequest;
use App\User\Application\InputPorts\ResetPasswordInterface;
use App\User\Infrastructure\OutputPorts\UserRepositoryInterface;
use App\User\Application\OutputPorts\PasswordResetRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Caso de uso para restablecer la contraseña de un usuario.
 */
class ResetPasswordUseCase implements ResetPasswordInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordResetRepositoryInterface $passwordResetRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * Restablece la contraseña de un usuario utilizando un token de verificación.
     *
     * Este métod valida el token de restablecimiento de contraseña proporcionado,
     * actualiza la contraseña del usuario si el token es válido y elimina el registro
     * de restablecimiento de contraseña utilizado.
     *
     * @param ResetPasswordRequest $request Objeto que contiene el email del usuario, el token y la nueva contraseña.
     *
     * @throws \Exception Si el token es inválido o ha expirado, o si el usuario no es encontrado.
     *
     * @return void
     */
    public function resetPassword(ResetPasswordRequest $request): void
    {
        $passwordReset = $this->passwordResetRepository->findByEmail($request->email);

        if (!$passwordReset || !$passwordReset->verifyToken($request->token)) {
            throw new \Exception("Código inválido o expirado.");
        }

        $user = $this->userRepository->findByEmail($request->email);

        if (!$user) {
            throw new \Exception("Usuario no encontrado.");
        }

        $hashedPassword = $this->passwordHasher->hashPassword($user, $request->newPassword);
        $user->setPassword($hashedPassword);

        $this->userRepository->save($user);

        $this->passwordResetRepository->remove($passwordReset);
    }
}