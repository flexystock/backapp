<?php
declare(strict_types=1);
namespace App\Client\Infrastructure\InputAdapters;

use App\Client\Application\GetAllClientsUseCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use App\Client\Infrastructure\InputPorts\GetAllClientsInputPort;
class GenericClientController
{
    private GetAllClientsInputPort $getAllClientsInputPort;

    public function __construct(GetAllClientsInputPort $getAllClientsInputPort){
        $this->getAllClientsInputPort = $getAllClientsInputPort;
    }

    #[Route('/api/clients', name: 'get_all_clients', methods: ['GET'])]
    #[OA\Get(
        path: '/api/clients',
        summary: 'Get All Clients',
        tags: ['Client'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Get All Clients successfully',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'clientName', type: 'string', example: 'cliente 1'),
                        ]
                    )
                )
            ),
             new OA\Response(
                response: 404,
                description: 'No Clients Found',
                content: new OA\JsonContent(
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Not Found Any Client'),
                    ]
                )
             ),
            new OA\Response(
                 response: 400,
                description: 'Invalid input'
            )
        ]
    )]
    public function getAllClients(): JsonResponse
    {
        $clients = $this->getAllClientsInputPort->getAll();
        $clientssArray = array_map(function ($client) {
            return [
                'name' => $client->getName()
            ];
        }, $clients);

        if (empty($clients)) {
            return $this->jsonResponse(['error' => 'Not Found Any Client'], JsonResponse::HTTP_OK);
        }else{
            return $this->jsonResponse($clientssArray, JsonResponse::HTTP_OK);
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