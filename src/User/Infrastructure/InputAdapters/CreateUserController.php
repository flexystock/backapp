<?php

declare(strict_types=1);

namespace App\User\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use App\User\Application\DTO\Management\CreateClientUserRequest;
use App\User\Application\InputPorts\CreateClientUserInputPort;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateUserController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly CreateClientUserInputPort $createClientUserInputPort,
        private readonly ValidatorInterface $validator,
        private readonly Security $security,
        PermissionService $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/user_create', name: 'user_create', methods: ['POST'])]
    #[RequiresPermission('user.create')]
    #[OA\Post(
        path: '/api/user_create',
        summary: 'Crear un usuario asociado a un cliente',
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
                response: Response::HTTP_CREATED,
                description: 'Usuario creado correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'USER_CREATED'),
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'uuid', type: 'string'),
                                new OA\Property(property: 'email', type: 'string', format: 'email'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: Response::HTTP_BAD_REQUEST, description: 'Datos inválidos'),
            new OA\Response(response: Response::HTTP_FORBIDDEN, description: 'Sin permisos suficientes'),
            new OA\Response(response: Response::HTTP_CONFLICT, description: 'Email ya en uso'),
            new OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Cliente o rol no encontrado'),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        if ($permissionCheck = $this->checkPermissionJson('user.create')) {
            return $permissionCheck;
        }

        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return $this->json(['message' => 'INVALID_JSON'], Response::HTTP_BAD_REQUEST);
        }

        $dto = new CreateClientUserRequest(
            (string) ($payload['uuidClient'] ?? ''),
            (string) ($payload['userEmail'] ?? ''),
            (string) ($payload['userRol'] ?? '')
        );

        $user = $this->security->getUser();
        if ($user && method_exists($user, 'getUuid')) {
            $dto->setCreatedByUuid($user->getUuid());
        }

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json([
                'message' => 'VALIDATION_FAILED',
                'errors' => $this->formatValidationErrors($errors),
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $createdUser = $this->createClientUserInputPort->create($dto);
        } catch (\RuntimeException $exception) {
            return $this->handleDomainException($exception);
        } catch (\Throwable $throwable) {
            return $this->json([
                'message' => 'USER_CREATION_FAILED',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json([
            'message' => 'USER_CREATED',
            'user' => [
                'uuid' => $createdUser->getUuid(),
                'email' => $createdUser->getEmail(),
            ],
        ], Response::HTTP_CREATED);
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
            'EMAIL_IN_USE' => $this->json(['message' => 'EMAIL_IN_USE'], Response::HTTP_CONFLICT),
            'CLIENT_NOT_FOUND' => $this->json(['message' => 'CLIENT_NOT_FOUND'], Response::HTTP_NOT_FOUND),
            'ROLE_NOT_FOUND' => $this->json(['message' => 'ROLE_NOT_FOUND'], Response::HTTP_NOT_FOUND),
            'INVALID_USER_DATA' => $this->json(['message' => 'INVALID_USER_DATA'], Response::HTTP_BAD_REQUEST),
            default => $this->json(['message' => 'USER_CREATION_FAILED'], Response::HTTP_INTERNAL_SERVER_ERROR),
        };
    }
}
