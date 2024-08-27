<?php
declare(strict_types=1);
namespace App\User\Infrastructure\InputAdapters;

use App\User\Application\GetAllUserUseCase;
use App\User\Infrastructure\InputPorts\GetAllUsersInputPort;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use OpenApi\Attributes as OA;
class GenericUserController
{
    private GetAllUsersInputPort $getAllUsersInputPort;

    public function __construct(GetAllUsersInputPort $getAllUsersInputPort)
    {
        $this->getAllUsersInputPort = $getAllUsersInputPort;
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
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'mail', type: 'string', example: 'john.doe@example.com'),
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: 'No Users Found',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Not Found Any User'),
                    ]
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
        //die("llegamos");
        $users = $this->getAllUsersInputPort->getAll();

        $usersArray = array_map(function ($user) {
            return [
                'mail' => $user->getEmail()
            ];
        }, $users);

        if (empty($users)) {
            return $this->jsonResponse(['error' => 'Not Found Any User'], JsonResponse::HTTP_OK);
        }else{
            return $this->jsonResponse($usersArray, JsonResponse::HTTP_OK);
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
}