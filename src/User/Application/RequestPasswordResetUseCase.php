<?php

// src/User/Application/RequestPasswordResetUseCase.php

namespace App\User\Application;

use App\User\Application\DTO\ForgotPasswordRequest;
use App\User\Infrastructure\OutputPorts\UserRepositoryInterface;
use App\User\Infrastructure\OutputPorts\PasswordResetRepositoryInterface;
use App\Entity\Main\PasswordReset;
use App\User\Infrastructure\OutputPorts\NotificationServiceInterface;

class RequestPasswordResetUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordResetRepositoryInterface $passwordResetRepository,
        private NotificationServiceInterface $emailService
    ) {}

    public function handle(ForgotPasswordRequest $request): void
    {
        $user = $this->userRepository->findByEmail($request->email);

        if (!$user) {
            // Por razones de seguridad, no reveles si el email no existe
            return;
        }

        // Generar un código seguro de 5 caracteres
        $token = bin2hex(random_bytes(3)); // Genera 6 caracteres hexadecimales

        $expiresAt = new \DateTimeImmutable('+15 minutes');

        $passwordReset = new PasswordReset($user->getEmail(), $token, $expiresAt);

        $this->passwordResetRepository->save($passwordReset);

        // Enviar el email con el código
        $this->emailService->sendPasswordResetEmail($user, $token);
    }
}
