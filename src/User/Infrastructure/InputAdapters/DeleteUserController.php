<?php

declare(strict_types=1);

namespace App\User\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use App\User\Application\DTO\Management\DeleteUserRequest;
use App\User\Application\InputPorts\DeleteUserInputPort;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DeleteUserController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly DeleteUserInputPort $deleteUserInputPort,
        private readonly ValidatorInterface $validator,
        PermissionService $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/user_delete', name: 'user_delete', methods: ['DELETE'])]
    #[RequiresPermission('user.delete')]
    #[OA\Delete(
        path: '/api/user_delete',
        summary: 'Eliminar un usuario asociado a un cliente',
        tags: ['User'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'userEmail'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'userEmail', type: 'string', format: 'email'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Usuario eliminado correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'USER_DELETED'),
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Datos inválidos o usuario no asociado al cliente'),
            new OA\Response(response: Response::HTTP_FORBIDDEN, description: 'Sin permisos suficientes'),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Usuario no encontrado'),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        if ($permissionCheck = $this->checkPermissionJson('user.delete')) {
            return $permissionCheck;
        }

        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return $this->json(['message' => 'INVALID_JSON'], Response::HTTP_BAD_REQUEST);
        }

        $dto = new DeleteUserRequest(
            (string) ($payload['uuidClient'] ?? ''),
            (string) ($payload['userEmail'] ?? ''),
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json([
                'message' => 'VALIDATION_FAILED',
                'errors' => $this->formatValidationErrors($errors),
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->deleteUserInputPort->delete($dto);
        } catch (\RuntimeException $exception) {
            return $this->handleDomainException($exception);
        } catch (\Throwable $throwable) {
            return $this->json([
                'message' => 'USER_DELETION_FAILED',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json([
            'message' => 'USER_DELETED',
        ]);
    }

    private function formatValidationErrors(ConstraintViolationListInterface $errors): array
    {
        $messages = [];
        foreach ($errors as $error) {
            $messages[$error->getPropertyPath()] = $error->getMessage();
        }

        return $messages;
    }

    private function handleDomainException(\RuntimeException $exception): JsonResponse
    {
        return match ($exception->getMessage()) {
            'USER_NOT_FOUND' => $this->json(['message' => 'USER_NOT_FOUND'], Response::HTTP_NOT_FOUND),
            'USER_NOT_ASSOCIATED_WITH_CLIENT' => $this->json(['message' => 'USER_NOT_ASSOCIATED_WITH_CLIENT'], Response::HTTP_BAD_REQUEST),
            default => $this->json(['message' => 'USER_DELETION_FAILED'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }
}
