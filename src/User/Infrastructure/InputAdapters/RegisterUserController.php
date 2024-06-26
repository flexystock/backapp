<?php
declare(strict_types=1);
namespace App\User\Infrastructure\InputAdapters;

use App\User\Application\RegisterUserUseCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use App\Client\Application\CreateClientUseCase;

class RegisterUserController
{
    private RegisterUserUseCase $registerUseCase;
    private CreateClientUseCase $createClientUseCase;

    public function __construct(RegisterUserUseCase $registerUseCase, CreateClientUseCase $createClientUseCase)
    {
        $this->registerUseCase = $registerUseCase;
        $this->createClientUseCase = $createClientUseCase;
    }

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    #[OA\Post(
        path: '/api/register',
        summary: 'Register a new user',
        tags: ['User'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'mail', type: 'string'),
                    new OA\Property(property: 'password', type: 'string'),
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'surnames', type: 'string'),
                    //new OA\Property(property: 'clientName', type: 'string'),
                    //new OA\Property(property: 'businessGroupName', type: 'string')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'User registered successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'status', type: 'string'),
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer'),
                                new OA\Property(property: 'mail', type: 'string'),
                                new OA\Property(property: 'name', type: 'string'),
                                new OA\Property(property: 'surnames', type: 'string')
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input'
            )
        ]
    )]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        try {
            $user = $this->registerUseCase->register($data);
            //$client = $this->createClientUseCase->create($data);
            $responseContent = [
                'message' => 'User registered successfully',
                'user' => $user, // Asumiendo que $user es un array o un objeto que se puede serializar
                //'client' => $client // Asumiendo que $client es un array o un objeto que se puede serializar
            ];
            return new JsonResponse($responseContent, Response::HTTP_CREATED);
            } catch (\Exception $e) {
                $responseContent = [
                    'message' => 'An error occurred',
                    'error' => $e->getMessage()
                ];
                return new JsonResponse($responseContent, Response::HTTP_BAD_REQUEST);
            }
    }
}