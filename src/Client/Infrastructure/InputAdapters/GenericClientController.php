<?php
declare(strict_types=1);
namespace App\Client\Infrastructure\InputAdapters;

use App\Entity\Main\Client;
use App\Client\Application\GetAllClientsUseCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use App\Client\Infrastructure\InputPorts\GetAllClientsInputPort;
use App\Client\Infrastructure\InputPorts\GetClientByUuidInputPort;
use App\Client\Infrastructure\InputPorts\GetClientByNameInputPort;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
class GenericClientController
{
    private GetAllClientsInputPort $getAllClientsInputPort;
    private GetClientByUuidInputPort $getClientByUuidInputPort;
    private GetClientByNameInputPort $getClientByNameInputPort;

    public function __construct(GetAllClientsInputPort $getAllClientsInputPort,
                                GetClientByUuidInputPort $getClientByUuidInputPort,
                                GetClientByNameInputPort $getClientByNameInputPort){
        $this->getAllClientsInputPort = $getAllClientsInputPort;
        $this->getClientByUuidInputPort = $getClientByUuidInputPort;
        $this->getClientByNameInputPort = $getClientByNameInputPort;
    }

    #[Route('/api/clients', name: 'get_clients', methods: ['GET'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    #[OA\Get(
        path: '/api/clients',
        summary: 'Get Clients',
        tags: ['Client'],
        parameters: [
            new OA\Parameter(
                name: 'uuid',
                in: 'query',
                required: false,
                description: 'UUID of the client to fetch',
                schema: new OA\Schema(type: 'string', example: '492e5a45-9ad9-4876-87f7-0788d842f17d')
            ),
            new OA\Parameter(
                name: 'name',
                in: 'query',
                required: false,
                description: 'Name of the client to fetch',
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
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'uuid_client', type: 'string', example: '492e5a45-9ad9-4876-87f7-0788d842f17d'),
                            new OA\Property(property: 'clientName', type: 'string', example: 'Restaurante Pepe'),
                        ]
                    )
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Client not found',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Client not found'),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input'
            )
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
            'uuid_client' => $client->getUuidClient(),
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
            'uuid_client' => $client->getUuidClient(),
            'clientName' => $client->getClientName(),
        ], 200);
    }

    private function getAllClients(): JsonResponse
    {
        $clients = $this->getAllClientsInputPort->getAll();

        // Convertir objetos Client en arrays
        $clientsArray = array_map(function ($client) {
            return [
                'uuid_client' => $client->getUuidClient(),
                'clientName' => $client->getClientName(),
            ];
        }, $clients);

        return $this->jsonResponse($clientsArray, 200);
    }


    #[Route('/api/clients/{uuid}', name: 'get_client_by_uuid', methods: ['GET'])]
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    #[OA\Get(
        path: '/api/clients/{uuid}',
        summary: 'Get Client By Uuid',
        tags: ['Client'],
        parameters: [
            new OA\Parameter(
                name: 'Uuid',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string'),
                description: 'Uuid del cliente a obtener'
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Get Client successfully',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'clientName', type: 'string', example: 'cliente 1'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Client Not Found',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Client Not Found'),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid input'
            )
        ]
    )]
//    public function getClientByUuid(string $uuid): JsonResponse
//    {
//        $client = $this->getClientByUuidInputPort->getByUuid($uuid);
//
//        if (!$client) {
//            return $this->jsonResponse(['error' => 'Client Not Found'], JsonResponse::HTTP_NOT_FOUND);
//        }
//
//        return $this->jsonResponse([
//            'clientName' => $client->getName()
//        ], JsonResponse::HTTP_OK);
//    }

    private function jsonResponse(array $data, int $status = 200): JsonResponse
    {
        $response = new JsonResponse($data, $status);
        $response->headers->set('Cache-Control', 'no-cache, private');
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        return $response;
    }
}