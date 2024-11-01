<?php

namespace App\User\Application;

use App\User\Application\DTO\ResetPasswordRequest;
use App\User\Domain\Repository\UserRepositoryInterface;
use App\User\Domain\Repository\PasswordResetRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordResetRepositoryInterface $passwordResetRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function handle(ResetPasswordRequest $request): void
    {
        $passwordReset = $this->passwordResetRepository->findByEmail($request->email);

        if (!$passwordReset || !$passwordReset->verifyToken($request->token)) {
            throw new \Exception("Código inválido o expirado.");
        }

        $user = $this->userRepository->findByEmail($request->email);

        if (!$user) {
            throw new \Exception("Usuario no encontrado.");
        }

        // Actualizar la contraseña
        $hashedPassword = $this->passwordHasher->hashPassword($user, $request->newPassword);
        $user->setPassword($hashedPassword);

        $this->userRepository->save($user);

        // Eliminar el registro de restablecimiento de contraseña
        $this->passwordResetRepository->remove($passwordReset);
    }
}