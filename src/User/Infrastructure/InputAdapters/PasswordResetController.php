<?php
// src/User/Infrastructure/InputAdapters/PasswordResetController.php

namespace App\User\Infrastructure\InputAdapters;

use App\User\Application\DTO\ForgotPasswordRequest;
use App\User\Application\DTO\ResetPasswordRequest;
use App\User\Application\InputPorts\ResetPasswordInterface;
use App\User\Application\InputPorts\RequestPasswordResetInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class PasswordResetController
{
    private RequestPasswordResetInterface $requestPasswordReset;
    private ResetPasswordInterface $passwordReset;
    private ValidatorInterface $validator;
    private RateLimiterFactory $passwordResetLimiter;
    public function __construct(RequestPasswordResetInterface $requestPasswordReset,
                                ResetPasswordInterface $passwordReset,
                                ValidatorInterface $validator,
                                RateLimiterFactory $passwordResetLimiter
                                ){
        $this->requestPasswordReset= $requestPasswordReset;
        $this->passwordReset = $passwordReset;
        $this->passwordResetLimiter = $passwordResetLimiter;
        $this->validator = $validator;
    }


    #[Route('/api/forgot-password', name: 'api_forgot_password', methods: ['POST'])]
    #[OA\Post(
        path: '/api/forgot-password',
        summary: 'Solicitar restablecimiento de contraseña',
        tags: ['User'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'usuario@example.com'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Si el email existe, se ha enviado un código de restablecimiento.',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Si el email existe, se ha enviado un código de restablecimiento.'),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Errores de validación',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'errors', type: 'string', example: 'El email es obligatorio.'),
                    ]
                )
            )
        ]
    )]
    public function forgotPassword(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $forgotPasswordRequest = new ForgotPasswordRequest();
        $forgotPasswordRequest->email = $data['email'] ?? '';

        $errors = $this->validator->validate($forgotPasswordRequest);

        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }

        $this->requestPasswordReset->requestPasswordReset($forgotPasswordRequest);

        return new JsonResponse(['message' => 'Si el email existe, se ha enviado un código de restablecimiento.'], 200);
    }

    #[Route('/api/reset-password', name: 'api_reset_password', methods: ['POST'])]
    #[OA\Post(
        path: '/api/reset-password',
        summary: 'Restablecer contraseña',
        tags: ['User'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'token', 'newPassword'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'usuario@example.com'),
                    new OA\Property(property: 'token', type: 'string', example: 'abc123'),
                    new OA\Property(property: 'newPassword', type: 'string', format: 'password', example: 'nuevaContraseña123'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Contraseña actualizada correctamente.',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Contraseña actualizada correctamente.'),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Errores de validación o código inválido.',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Código inválido o expirado.'),
                    ]
                )
            )
        ]
    )]
    public function resetPassword(Request $request): JsonResponse
    {
        // Obtener el limitador basado en la IP del cliente o en otro identificador
        $limiter = $this->passwordResetLimiter->create($request->getClientIp());
        // Intentar consumir un token del limitador
        $limit = $limiter->consume(1);

        if (!$limit->isAccepted()) {
            // Si no hay tokens disponibles, lanzar una excepción o manejar el error
            $retryAfter = $limit->getRetryAfter()->getTimestamp() - time();
            throw new TooManyRequestsHttpException($retryAfter, 'Demasiados intentos. Por favor, inténtelo de nuevo en 15 minutos.');
        }

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
            $this->passwordReset->resetPassword($resetPasswordRequest);
            return new JsonResponse(['message' => 'Contraseña actualizada correctamente.'], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}
