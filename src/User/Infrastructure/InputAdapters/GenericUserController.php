<?php
declare(strict_types=1);
namespace App\User\Infrastructure\InputAdapters;

use App\User\Application\InputPorts\GetAllUsersInputPort;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\SerializerInterface;
use App\User\Application\InputPorts\GetUserClientsInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class GenericUserController extends AbstractController
{
    private GetAllUsersInputPort $getAllUsersInputPort;
    private GetUserClientsInterface $getUserClientsUseCase;
    private SerializerInterface $serializer;
    private Security $security;
    private UserRepositoryInterface $userRepository;

    public function __construct(GetAllUsersInputPort $getAllUsersInputPort, Security $security,
                                GetUserClientsInterface $getUserClientsUseCase,
                                SerializerInterface $serializer,
                                UserRepositoryInterface $userRepository)
    {
        $this->getAllUsersInputPort = $getAllUsersInputPort;
        $this->security = $security;
        $this->getUserClientsUseCase = $getUserClientsUseCase;
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
                                example: 'john.doe@example.com'),
                        ],
                        type: 'object')
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
            )
        ]
    )]
    public function getAllUsers(): JsonResponse
    {
        $users = $this->getAllUsersInputPort->getAll();

        $usersArray = array_map(function ($user) {
            return [
                'email' => $user->getEmail()
            ];
        }, $users);

        if (empty($users)) {
            return $this->jsonResponse(['error' => 'Not Found Any User'], Response::HTTP_OK);
        }else{
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
            return new JsonResponse(['error' => 'Usuario no autenticado'], Response::HTTP_UNAUTHORIZED);
        }
        // Obtener el usuario desde el repositorio para asegurarnos de que está gestionado por Doctrine
        $userEntity = $this->userRepository->findByUuid($user->getUuid());

        if (!$userEntity) {
            return new JsonResponse(['error' => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }
        // Obtener los clientes asociados
        try {
            $clientDTOs = $this->getUserClientsUseCase->getUserClients($userEntity->getUuid());

            // Serializar los datos
            $data = $this->serializer->serialize($clientDTOs, 'json', ['groups' => ['client']]);

            return new JsonResponse(json_decode($data), Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}