<?php

declare(strict_types=1);

namespace App\User\Infrastructure\InputAdapters;

use App\Entity\Main\User;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use App\User\Application\DTO\Management\UpdateUserRoleRequest;
use App\User\Application\InputPorts\UpdateUserRoleInputPort;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateUserRoleController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly UpdateUserRoleInputPort $updateUserRoleInputPort,
        private readonly ValidatorInterface $validator,
        PermissionService $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/user_role_update', name: 'user_role_update', methods: ['PUT'])]
    #[RequiresPermission('user.update')]
    #[OA\Put(
        path: '/api/user_role_update',
        summary: 'Actualizar el rol de un usuario asociado a un cliente',
        tags: ['User'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'userEmail', 'userRol'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'userEmail', type: 'string', format: 'email'),
                    new OA\Property(property: 'userRol', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: 'Rol del usuario actualizado correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'USER_ROLE_UPDATED'),
                        new OA\Property(property: 'userRol', type: 'string', example: 'admin'),
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Datos inválidos o usuario no asociado al cliente'),
            new OA\Response(response: Response::HTTP_FORBIDDEN, description: 'Sin permisos suficientes'),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Usuario o rol no encontrado'),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        if ($permissionCheck = $this->checkPermissionJson('user.update')) {
            return $permissionCheck;
        }

        $currentUser = $this->getUser();
        if (!$currentUser instanceof User || null === $currentUser->getUuid()) {
            return $this->json(['message' => 'USER_NOT_AUTHENTICATED'], Response::HTTP_UNAUTHORIZED);
        }

        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return $this->json(['message' => 'INVALID_JSON'], Response::HTTP_BAD_REQUEST);
        }

        $dto = new UpdateUserRoleRequest(
            (string) ($payload['uuidClient'] ?? ''),
            (string) ($payload['userEmail'] ?? ''),
            (string) ($payload['userRol'] ?? ''),
            $currentUser->getUuid(),
        );

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json([
                'message' => 'VALIDATION_FAILED',
                'errors' => $this->formatValidationErrors($errors),
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $updatedRole = $this->updateUserRoleInputPort->update($dto);
        } catch (\RuntimeException $exception) {
            return $this->handleDomainException($exception);
        } catch (\Throwable $throwable) {
            return $this->json([
                'message' => 'USER_ROLE_UPDATE_FAILED',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json([
            'message' => 'USER_ROLE_UPDATED',
            'userRol' => $updatedRole,
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
            'ROLE_NOT_FOUND' => $this->json(['message' => 'ROLE_NOT_FOUND'], Response::HTTP_NOT_FOUND),
            default => $this->json(['message' => 'USER_ROLE_UPDATE_FAILED'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }
}
