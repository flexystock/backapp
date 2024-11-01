<?php
// src/User/Infrastructure/InputAdapters/PasswordResetController.php

namespace App\User\Infrastructure\InputAdapters;

use App\User\Application\DTO\ForgotPasswordRequest;
use App\User\Application\DTO\ResetPasswordRequest;
use App\User\Application\RequestPasswordResetUseCase;
use App\User\Application\ResetPasswordUseCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PasswordResetController
{
    public function __construct(
        private RequestPasswordResetUseCase $requestPasswordResetUseCase,
        private ResetPasswordUseCase $resetPasswordUseCase,
        private ValidatorInterface $validator
    ) {}

    #[Route('/api/forgot-password', name: 'api_forgot_password', methods: ['POST'])]
    public function forgotPassword(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $forgotPasswordRequest = new ForgotPasswordRequest();
        $forgotPasswordRequest->email = $data['email'] ?? '';

        $errors = $this->validator->validate($forgotPasswordRequest);

        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }

        $this->requestPasswordResetUseCase->handle($forgotPasswordRequest);

        return new JsonResponse(['message' => 'Si el email existe, se ha enviado un código de restablecimiento.'], 200);
    }

    #[Route('/api/reset-password', name: 'api_reset_password', methods: ['POST'])]
    public function resetPassword(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $resetPasswordRequest = new ResetPasswordRequest();
        $resetPasswordRequest->email = $data['email'] ?? '';
        $resetPasswordRequest->token = $data['token'] ?? '';
        $resetPasswordRequest->newPassword = $data['newPassword'] ?? '';

        $errors = $this->validator->validate($resetPasswordRequest);

        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }

        try {
            $this->resetPasswordUseCase->handle($resetPasswordRequest);
            return new JsonResponse(['message' => 'Contraseña actualizada correctamente.'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}
