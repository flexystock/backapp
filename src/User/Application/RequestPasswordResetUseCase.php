<?php

namespace App\User\Application;

use App\Entity\Main\PasswordReset;
use App\User\Application\DTO\ForgotPasswordRequest;
use App\User\Application\InputPorts\RequestPasswordResetInterface;
use App\User\Application\OutputPorts\NotificationServiceInterface;
use App\User\Application\OutputPorts\PasswordResetRepositoryInterface;
use App\User\Infrastructure\OutputPorts\UserRepositoryInterface;

class RequestPasswordResetUseCase implements RequestPasswordResetInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordResetRepositoryInterface $passwordResetRepository,
        private NotificationServiceInterface $emailService
    ) {}

    public function requestPasswordReset(ForgotPasswordRequest $request): void
    {
        $user = $this->userRepository->findByEmail($request->email);

        if (!$user) {
            // No revelar si el email no existe
            return;
        }

        // **Eliminar registros existentes de PasswordReset para este email**
        $this->passwordResetRepository->removeAllByEmail($user->getEmail());

        // Generar token
        $token = bin2hex(random_bytes(3)); // 6 caracteres hexadecimales

        $expiresAt = new \DateTimeImmutable('+15 minutes');

        $passwordReset = new PasswordReset($user->getEmail(), $token, $expiresAt);

        $this->passwordResetRepository->save($passwordReset);

        $this->emailService->sendPasswordResetEmail($user, $token);
    }
}