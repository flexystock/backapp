<?php

namespace App\Order\Infrastructure\InputAdapters;

use App\Order\Application\DTO\GetAllOrdersRequest;
use App\Order\Application\InputPorts\GetAllOrdersUseCaseInterface;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetAllOrdersController extends AbstractController
{
    use PermissionControllerTrait;

    private GetAllOrdersUseCaseInterface $getAllOrdersUseCase;
    private LoggerInterface $logger;

    public function __construct(
        GetAllOrdersUseCaseInterface $getAllOrdersUseCase,
        LoggerInterface $logger,
        PermissionService $permissionService
    ) {
        $this->getAllOrdersUseCase = $getAllOrdersUseCase;
        $this->logger = $logger;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/orders', name: 'api_get_all_orders', methods: ['POST'])]
    #[RequiresPermission('order.view')]
    #[OA\Post(
        path: '/api/orders',
        summary: 'Obtener todos los pedidos de un cliente',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid', example: 'c014a415-4113-49e5-80cb-cc3158c15236'),
                    new OA\Property(property: 'status', type: 'string', enum: ['draft', 'pending', 'sent', 'confirmed', 'received', 'cancelled'], example: 'pending', nullable: true),
                    new OA\Property(property: 'supplierId', type: 'integer', example: 1, nullable: true),
                ],
                type: 'object'
            )
        ),
        tags: ['Order'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de pedidos devuelta con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'orders',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'order_number', type: 'string', example: 'ORD-20231213-0001'),
                                    new OA\Property(property: 'status', type: 'string', example: 'pending'),
                                    new OA\Property(property: 'total_amount', type: 'number', format: 'float', example: 150.50),
                                    new OA\Property(property: 'currency', type: 'string', example: 'EUR'),
                                    new OA\Property(property: 'delivery_date', type: 'string', format: 'date', example: '2023-12-15'),
                                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2023-12-13 10:30:00'),
                                    new OA\Property(
                                        property: 'items',
                                        type: 'array',
                                        items: new OA\Items(
                                            properties: [
                                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                                new OA\Property(property: 'product_id', type: 'integer', example: 5),
                                                new OA\Property(property: 'quantity', type: 'number', format: 'float', example: 10.5),
                                                new OA\Property(property: 'unit', type: 'string', example: 'kg'),
                                                new OA\Property(property: 'unit_price', type: 'number', format: 'float', example: 12.50),
                                                new OA\Property(property: 'subtotal', type: 'number', format: 'float', example: 131.25),
                                            ],
                                            type: 'object'
                                        )
                                    ),
                                ],
                                type: 'object'
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Falta el campo uuidClient',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'uuidClient is required'),
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
                response: 403,
                description: 'El usuario no tiene acceso al cliente especificado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Access denied to the specified client'),
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
    public function invoke(Request $request): JsonResponse
    {
        $permissionCheck = $this->checkPermissionJson('order.view');
        if ($permissionCheck) {
            return $permissionCheck;
        }

        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;

        if (!$uuidClient) {
            $this->logger->warning('[GetAllOrdersController] uuidClient not provided.');
            return new JsonResponse(['error' => 'uuidClient is required'], 400);
        }

        // Validate UUID format
        if (!$this->isValidUuid($uuidClient)) {
            $this->logger->warning('[GetAllOrdersController] Invalid uuidClient format.');
            return new JsonResponse(['error' => 'Invalid uuid format'], 400);
        }

        // Verify user has access to the client
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        if (!$user->getClients()->exists(function ($key, $client) use ($uuidClient) {
            return $client->getUuidClient() === $uuidClient;
        })) {
            $this->logger->warning('[GetAllOrdersController] User does not have access to the specified client.');
            return new JsonResponse(['error' => 'Access denied to the specified client'], 403);
        }

        try {
            // Get optional filters
            $status = $data['status'] ?? null;
            $supplierId = $data['supplierId'] ?? null;

            // Create request DTO
            $getAllOrdersRequest = new GetAllOrdersRequest($uuidClient, $status, $supplierId);

            // Execute use case
            $response = $this->getAllOrdersUseCase->execute($getAllOrdersRequest);

            if ($response->getError()) {
                return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
            }

            return new JsonResponse(['orders' => $response->getOrders()], $response->getStatusCode());

        } catch (\Exception $e) {
            $this->logger->error('[GetAllOrdersController] Error retrieving orders.', ['exception' => $e]);
            return new JsonResponse(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Validate UUID format.
     */
    private function isValidUuid(string $uuid): bool
    {
        return 1 === preg_match('/^[0-9a-fA-F-]{36}$/', $uuid);
    }
}
