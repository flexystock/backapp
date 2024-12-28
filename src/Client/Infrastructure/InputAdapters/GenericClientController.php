<?php

declare(strict_types=1);

namespace App\Client\Infrastructure\InputAdapters;

use App\Client\Application\InputPorts\CreateClientInputPort;
use App\Client\Application\InputPorts\GetAllClientsInputPort;
use App\Client\Application\InputPorts\GetClientByNameInputPort;
use App\Client\Application\InputPorts\GetClientByUuidInputPort;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class GenericClientController
{
    private GetAllClientsInputPort $getAllClientsInputPort;
    private GetClientByUuidInputPort $getClientByUuidInputPort;
    private GetClientByNameInputPort $getClientByNameInputPort;
    private CreateClientInputPort $createClientInputPort;

    public function __construct(GetAllClientsInputPort $getAllClientsInputPort,
        GetClientByUuidInputPort $getClientByUuidInputPort,
        GetClientByNameInputPort $getClientByNameInputPort,
        CreateClientInputPort $createClientInputPort)
    {
        $this->getAllClientsInputPort = $getAllClientsInputPort;
        $this->getClientByUuidInputPort = $getClientByUuidInputPort;
        $this->getClientByNameInputPort = $getClientByNameInputPort;
        $this->createClientInputPort = $createClientInputPort;
    }

    #[Route('/api/clients', name: 'get_clients', methods: ['GET'])]
    #[OA\Get(
        path: '/api/clients',
        summary: 'Get Clients',
        tags: ['Client'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                description: 'UUID of the client to fetch',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', example: '492e5a45-9ad9-4876-87f7-0788d842f17d')
            ),
            new OA\Parameter(
                name: 'name',
                description: 'Name of the client to fetch',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string', example: 'Restaurante Pepe')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Get Clients successfully',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'uuidClient', type: 'string', example: '492e5a45-9ad9-4876-87f7-0788d842f17d'),
                            new OA\Property(property: 'clientName', type: 'string', example: 'Restaurante Pepe'),
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Client not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Client not found'),
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
    public function getClients(Request $request): JsonResponse
    {
        $uuid = $request->query->get('uuid');
        $name = $request->query->get('name');

        // Si se pasa un UUID, obtener cliente por UUID
        if ($uuid) {
            return $this->getClientByUuid($uuid);
        }

        // Si se pasa un nombre, obtener cliente por nombre
        if ($name) {
            return $this->getClientByName($name);
        }

        // Si no hay filtros, devolver todos los clientes
        return $this->getAllClients();
    }

    private function getClientByUuid(string $uuid): JsonResponse
    {
        $client = $this->getClientByUuidInputPort->getByUuid($uuid);
        if (!$client) {
            return $this->jsonResponse(['error' => 'Client not found'], 404);
        }

        return $this->jsonResponse([
            'uuidClient' => $client->getUuidClient(),
            'clientName' => $client->getClientName(),
        ], 200);
    }

    private function getClientByName(string $name): JsonResponse
    {
        $client = $this->getClientByNameInputPort->getByName($name);
        if (!$client) {
            return $this->jsonResponse(['error' => 'Client not found'], 404);
        }

        return $this->jsonResponse([
            'uuidClient' => $client->getUuidClient(),
            'clientName' => $client->getClientName(),
        ], 200);
    }

    private function getAllClients(): JsonResponse
    {
        $clients = $this->getAllClientsInputPort->getAll();

        // Convertir objetos Client en arrays
        $clientsArray = array_map(function ($client) {
            return [
                'uuidClient' => $client->getUuidClient(),
                'clientName' => $client->getClientName(),
            ];
        }, $clients);

        return $this->jsonResponse($clientsArray, 200);
    }

    #[Route('/api/clients', name: 'create_client', methods: ['POST'])]
    #[OA\Post(
        path: '/api/clients',
        summary: 'Create a new client',
        requestBody: new OA\RequestBody(
            description: 'Client name is required',
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Restaurante Pepe'),
                ],
                type: 'object'
            )
        ),
        tags: ['Client'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Client created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'uuidClient', type: 'string', example: '492e5a45-9ad9-4876-87f7-0788d842f17d'),
                        new OA\Property(property: 'clientName', type: 'string', example: 'Restaurante Pepe'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Client name is required',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Client name is required'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Error creating client',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Error creating client: ...'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function createClient(Request $request): JsonResponse
    {
        // Obtener el nombre del cliente del cuerpo de la solicitud (JSON)
        $data = json_decode($request->getContent(), true);
        $name = $data['name'] ?? null;

        // Validar que el nombre es proporcionado
        if (!$name) {
            return $this->jsonResponse(['error' => 'Client name is required'], 400);
        }

        // Crear el cliente utilizando el caso de uso correspondiente
        try {
            $newClient = $this->createClientInputPort->create($name);

            return $this->jsonResponse([
                'message' => 'Client created successfully',
                'client' => [
                    'uuidClient' => $newClient->getUuidClient(),
                    'clientName' => $newClient->getClientName(),
                ],
            ], 201); // 201 indica que un recurso fue creado
        } catch (\Exception $e) {
            // Manejar cualquier error que ocurra durante la creación
            return $this->jsonResponse(['error' => 'Error creating client: '.$e->getMessage()], 500);
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
