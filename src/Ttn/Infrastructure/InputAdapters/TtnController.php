<?php

namespace App\Ttn\Infrastructure\InputAdapters;

use App\Client\Application\InputPorts\GetClientByUuidInputPort;
use App\Ttn\Application\DTO\RegisterTtnAppRequest;
use App\Ttn\Application\DTO\RegisterTtnDeviceRequest;
use App\Ttn\Application\InputPorts\GetAllTtnDevicesUseCaseInterface;
use App\Ttn\Application\InputPorts\RegisterTtnAppUseCaseInterface;
use App\Ttn\Application\InputPorts\RegisterTtnDeviceUseCaseInterface;
use OpenApi\Attributes as OA;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TtnController extends AbstractController
{
    private RegisterTtnDeviceUseCaseInterface $registerTtnDeviceUseCase;
    private RegisterTtnAppUseCaseInterface $registerTtnAppUseCase;
    private GetClientByUuidInputPort $getClientByUuidInputPort;
    private GetAllTtnDevicesUseCaseInterface $getAllTtnDevicesUseCase;

    public function __construct(RegisterTtnDeviceUseCaseInterface $registerTtnDeviceUseCase,
        RegisterTtnAppUseCaseInterface $registerTtnAppUseCase,
        GetClientByUuidInputPort $getClientByUuidInputPort,
        GetAllTtnDevicesUseCaseInterface $getAllTtnDevicesUseCase)
    {
        $this->registerTtnDeviceUseCase = $registerTtnDeviceUseCase;
        $this->registerTtnAppUseCase = $registerTtnAppUseCase;
        $this->getClientByUuidInputPort = $getClientByUuidInputPort;
        $this->getAllTtnDevicesUseCase = $getAllTtnDevicesUseCase;
    }

    #[Route('/api/app_register', name: 'api_app_register', methods: ['POST'])]
    public function registerTtnApp(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }
        $uuidUser = $this->getUser()->getUuid();
        $uuidClient = $data['uuid_client'] ?? null;

        $client = $this->getClientByUuidInputPort->getByUuid($uuidClient);
        if (!$client) {
            return new JsonResponse(['error' => 'Client not found'], Response::HTTP_BAD_REQUEST);
        }

        $applicationId = 'prueba4-'.$client->getClientName();
        $applicationName = 'prueba4-'.$client->getClientName();
        $description = 'prueba4-app-'.$client->getClientName();
        $dto = new RegisterTtnAppRequest(
            $applicationId,
            $applicationName,
            $description,
            $uuidUser,
            new \DateTime(),
            $uuidClient);
        $response = $this->registerTtnAppUseCase->execute($dto);

        if ($response->isSuccess()) {
            return new JsonResponse(['message' => 'Application registered successfully'], Response::HTTP_OK);
        } else {
            return new JsonResponse(['error' => $response->getError()], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @throws RandomException
     */
    #[Route('/api/device_register', name: 'api_device_register', methods: ['POST'])]
    public function registerTtnDevice(): JsonResponse
    {
        $uuidUser = $this->getUser()->getUuid();
        $deviceId = 'heltec-ab01-'.random_int(1000, 9999);
        $dtoDevice = new RegisterTtnDeviceRequest($deviceId, $uuidUser, new \DateTime(), null, null, null);
        $response = $this->registerTtnDeviceUseCase->execute($dtoDevice);

        if ($response->isSuccess()) {
            return new JsonResponse(['message' => 'Device registered successfully'], Response::HTTP_OK);
        } else {
            return new JsonResponse(['error' => $response->getError()], status: Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/devices', name: 'api_devices', methods: ['GET'])]
    #[OA\Get(
        path: '/api/devices',
        description: 'Este endpoint devuelve un listado paginado de los dispositivos. 
                  Permite filtrar el campo "available" por `true` o `false`. 
                  Si se envía vacío, devuelve todos los dispositivos.',
        summary: 'Obtener dispositivos con paginación y filtrado por "available"',
        requestBody: new OA\RequestBody(
            description: 'Envío opcional de "available". 
                      Puede ser `true`, `false`, o vacío. 
                      Si es vacío o no se envía, se devuelven todos.',
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'available',
                        type: 'string',
                        enum: ['true', 'false', ''],
                        example: 'true'
                    ),
                ],
                type: 'object'
            )
        ),
        tags: ['Devices'],
        parameters: [
            new OA\Parameter(
                name: 'page',
                description: 'Número de página a obtener (empieza en 1)',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 1)
            ),
            new OA\Parameter(
                name: 'limit',
                description: 'Número de elementos por página',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', default: 10)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Listado devuelto correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'totalItems', type: 'integer', example: 45),
                        new OA\Property(property: 'itemsPerPage', type: 'integer', example: 10),
                        new OA\Property(property: 'currentPage', type: 'integer', example: 1),
                        new OA\Property(
                            property: 'devices',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'uuid', type: 'string', example: 'c014a415-4113-49e5-80cb-cc3158c15236'),
                                    new OA\Property(property: 'available', type: 'boolean', example: true),
                                    new OA\Property(property: 'end_device_id', type: 'string', example: 'heltec-ab01-1234'),
                                    new OA\Property(property: 'app_eui', type: 'string', example: '70B3D57ED004A75B'),
                                    new OA\Property(property: 'dev_eui', type: 'string', example: '70B3D57ED004A75C'),
                                    new OA\Property(property: 'app_key', type: 'string', example: '11223344556677889900AABBCCDDEEFF'),
                                ],
                                type: 'object'
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'No autenticado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Unauthorized'),
                    ],

                    type: 'object'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Error interno del servidor',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Internal Server Error'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function getAllDevices(Request $request): JsonResponse
    {
        // Verificar que el usuario tiene acceso al cliente
        $data = json_decode($request->getContent(), true);
        // Valor crudo (string, bool, null, dependiendo de Postman/Front)
        $availableRaw = $data['available'] ?? null;

        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
        // 2) Traducir el $availableRaw a ?bool
        //    Manejamos 3 casos:
        //      - "" => null
        //      - "true", true => true
        //      - "false", false => false
        $available = null;

        if (true === $availableRaw || 'true' === $availableRaw) {
            $available = true;
        } elseif (false === $availableRaw || 'false' === $availableRaw) {
            $available = false;
        }
        // Si es "" o null => se queda en null, mostrando "todos"

        try {
            $page = $request->query->getInt('page', 1);
            $limit = $request->query->getInt('limit', 10);

            $response = $this->getAllTtnDevicesUseCase->executePaginated($page, $limit, $available);

            return new JsonResponse([
                'totalItems' => $response->getMeta()['totalItems'],
                'itemsPerPage' => $response->getMeta()['itemsPerPage'],
                'currentPage' => $response->getMeta()['currentPage'],
                'devices' => $response->getDevices(),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error(
                'TtnController: Error al obtener los dispositivos paginados.',
                ['exception' => $e]
            );

            return new JsonResponse(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
