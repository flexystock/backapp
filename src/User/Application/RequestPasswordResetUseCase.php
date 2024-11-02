<?php

namespace App\User\Application;

use App\User\Application\DTO\ForgotPasswordRequest;
use App\User\Application\InputPorts\RequestPasswordResetInterface;
use App\User\Infrastructure\OutputPorts\UserRepositoryInterface;
use App\User\Application\OutputPorts\PasswordResetRepositoryInterface;
use App\User\Infrastructure\OutputPorts\NotificationServiceInterface;
use App\Entity\Main\PasswordReset;

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
        //die("email encotrado");
        if (!$user) {
            // No revelar si el email no existe
            return;
        }

        // Generar token
        $token = bin2hex(random_bytes(3)); // 6 caracteres hexadecimales

        $expiresAt = new \DateTimeImmutable('+15 minutes');

        $passwordReset = new PasswordReset($user->getEmail(), $token, $expiresAt);
        //die("llegamos hasta aqui");
        $this->passwordResetRepository->save($passwordReset);

        $this->emailService->sendPasswordResetEmail($user, $token);
    }
}