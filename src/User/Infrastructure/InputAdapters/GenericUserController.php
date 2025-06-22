<?php

declare(strict_types=1);

namespace App\User\Infrastructure\InputAdapters;

use App\User\Application\InputPorts\GetAllUsersInputPort;
use App\User\Application\InputPorts\GetUserClientsInterface;
use App\User\Application\InputPorts\GetUsersByClientInputPort;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class GenericUserController extends AbstractController
{
    private GetAllUsersInputPort $getAllUsersInputPort;
    private GetUserClientsInterface $getUserClientsUseCase;
    private GetUsersByClientInputPort $getUsersByClientUseCase;
    private SerializerInterface $serializer;
    private Security $security;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        GetAllUsersInputPort $getAllUsersInputPort,
        Security $security,
        GetUserClientsInterface $getUserClientsUseCase,
        GetUsersByClientInputPort $getUsersByClientUseCase,
        SerializerInterface $serializer,
        UserRepositoryInterface $userRepository
    ) {
        $this->getAllUsersInputPort = $getAllUsersInputPort;
        $this->security = $security;
        $this->getUserClientsUseCase = $getUserClientsUseCase;
        $this->getUsersByClientUseCase = $getUsersByClientUseCase;
        $this->serializer = $serializer;
        $this->userRepository = $userRepository;
    }

    #[Route('/api/users', name: 'get_all_users', methods: ['GET'])]
    #[OA\Get(
        path: '/api/users',
        summary: 'Get All Users',
        tags: ['User'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Get All Users successfully',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(
                                property: 'email',
                                type: 'string',
                                example: 'john.doe@example.com'
                            ),
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: 'No Users Found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Not Found Any User'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input'
            ),
        ]
    )]
    public function getAllUsers(): JsonResponse
    {
        $users = $this->getAllUsersInputPort->getAll();

        $usersArray = array_map(function ($user) {
            return [
                'email' => $user->getEmail(),
            ];
        }, $users);

        if (empty($users)) {
            return $this->jsonResponse(['message' => 'NOT_FOUND_ANY_USER'], Response::HTTP_OK);
        } else {
            return $this->jsonResponse($usersArray, Response::HTTP_OK);
        }
    }

    private function jsonResponse(array $data, int $status = 200): JsonResponse
    {
        $response = new JsonResponse($data, $status);
        $response->headers->set('Cache-Control', 'no-cache, private');
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }

    #[Route('/api/user/clients', name: 'get_user_clients', methods: ['GET'])]
    #[OA\Get(
        path: '/api/user/clients',
        summary: 'Obtener los clientes asociados al usuario autenticado',
        tags: ['User', 'Client'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de clientes asociados al usuario',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'uuid', type: 'string', example: 'c014a415-4113-49e5-80cb-cc3158c15236'),
                            new OA\Property(property: 'name', type: 'string', example: 'barpepe'),
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Usuario no autenticado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Usuario no autenticado'),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Error interno del servidor',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Mensaje de error'),
                    ]
                )
            ),
        ]
    )]
    public function getUserClients(): JsonResponse
    {
        $user = $this->security->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'USER_NOT_AUTHENTICATED'], Response::HTTP_UNAUTHORIZED);
        }
        // Obtener el usuario desde el repositorio para asegurarnos de que está gestionado por Doctrine
        $userEntity = $this->userRepository->findByUuid($user->getUuid());

        if (!$userEntity) {
            return new JsonResponse(['message' => 'USER_NOT_FOUND_CLIENTS'], Response::HTTP_NOT_FOUND);
        }
        // Obtener los clientes asociados
        try {
            $clientDTOs = $this->getUserClientsUseCase->getUserClients($userEntity->getUuid());

            // Serializar los datos
            $data = $this->serializer->serialize($clientDTOs, 'json', ['groups' => ['client']]);

            return new JsonResponse(json_decode($data), Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/users_client', name: 'get_users_by_client', methods: ['POST'])]
    #[OA\Get(
        path: '/api/users_client',
        summary: 'Get Users for a client',
        tags: ['User'],
        parameters: [
            new OA\Parameter(name: 'uuidClient', in: 'query', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'List of users'),
            new OA\Response(response: 403, description: 'Access denied'),
            new OA\Response(response: 404, description: 'No Users Found')
        ]
    )]
    public function getUsersByClient(Request $request): JsonResponse
    {
        if (!$this->isGranted('ROLE_ROOT') && !$this->isGranted('ROLE_SUPERADMIN') && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('No tienes permiso.');
        }

        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;

        if (!$uuidClient) {
            return $this->jsonResponse(['message' => 'CLIENT_UUID_REQUIRED'], Response::HTTP_BAD_REQUEST);
        }

        $users = $this->getUsersByClientUseCase->getUsersByClient($uuidClient);

        $usersArray = array_map(function ($user) {
            return ['email' => $user->getEmail(),
                    'name' => $user->getName(),
                    'role' => $user->getRoles()];
        }, $users);

        if (empty($users)) {
            return $this->jsonResponse(['message' => 'NOT_FOUND_ANY_USER'], Response::HTTP_OK);
        }

        return $this->jsonResponse($usersArray, Response::HTTP_OK);
    }
}
