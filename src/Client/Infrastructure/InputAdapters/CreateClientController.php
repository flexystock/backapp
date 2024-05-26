<?php
declare(strict_types=1);

namespace App\Client\Infrastructure\InputAdapters;

use App\Client\Infrastructure\InputPorts\CreateClientInputPort;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Attributes as OA;

class CreateClientController
{
    private CreateClientInputPort $createInputPort;

    public function __construct(CreateClientInputPort $createInputPort)
    {
        $this->createInputPort = $createInputPort;
    }
    #[Route('/api/client/create', name: 'create_client', methods: ['POST'])]
    #[OA\Post(
        path: '/api/client/create',
        summary: 'Create a new Client',
        tags: ['Client'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: 'object',
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Clientsuccessfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'status', type: 'string'),
                        new OA\Property(
                            property: 'client',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'name', type: 'string'),
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
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        try {
            $client = $this->createInputPort->create($data);
            return new Response('Client registered successfully', Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}