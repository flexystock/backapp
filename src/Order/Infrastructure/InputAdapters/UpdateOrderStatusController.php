<?php

namespace App\Order\Infrastructure\InputAdapters;

use App\Order\Application\DTO\UpdateOrderStatusRequest;
use App\Order\Application\InputPorts\UpdateOrderStatusUseCaseInterface;
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

class UpdateOrderStatusController extends AbstractController
{
    use PermissionControllerTrait;

    private UpdateOrderStatusUseCaseInterface $updateOrderStatusUseCase;
    private LoggerInterface $logger;

    public function __construct(
        UpdateOrderStatusUseCaseInterface $updateOrderStatusUseCase,
        LoggerInterface $logger,
        PermissionService $permissionService
    ) {
        $this->updateOrderStatusUseCase = $updateOrderStatusUseCase;
        $this->logger = $logger;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/order/status', name: 'api_update_order_status', methods: ['PUT'])]
    #[RequiresPermission('order.update')]
    #[OA\Put(
        path: '/api/order/status',
        summary: 'Actualizar el estado de un pedido',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'orderId', 'status'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid', example: 'c014a415-4113-49e5-80cb-cc3158c15236'),
                    new OA\Property(property: 'orderId', type: 'integer', example: 1),
                    new OA\Property(property: 'status', type: 'string', enum: ['draft', 'pending', 'sent', 'confirmed', 'received', 'cancelled'], example: 'sent'),
                    new OA\Property(property: 'notes', type: 'string', example: 'Pedido enviado al proveedor', nullable: true),
                    new OA\Property(property: 'cancellationReason', type: 'string', example: 'Producto ya no necesario', nullable: true),
                ],
                type: 'object'
            )
        ),
        tags: ['Order'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Estado del pedido actualizado con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'order',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'order_number', type: 'string', example: 'ORD-20231213-0001'),
                                new OA\Property(property: 'status', type: 'string', example: 'sent'),
                                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2023-12-13 15:30:00'),
                            ],
                            type: 'object'
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Faltan campos requeridos o formato inválido',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Missing required fields'),
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
                response: 404,
                description: 'Pedido no encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Order not found'),
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
        $permissionCheck = $this->checkPermissionJson('order.update');
        if ($permissionCheck) {
            return $permissionCheck;
        }

        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;
        $orderId = $data['orderId'] ?? null;
        $status = $data['status'] ?? null;

        if (!$uuidClient || !$orderId || !$status) {
            $this->logger->warning('[UpdateOrderStatusController] Missing required fields.');
            return new JsonResponse(['error' => 'Missing required fields: uuidClient, orderId, and status'], 400);
        }

        // Validate UUID format
        if (!$this->isValidUuid($uuidClient)) {
            $this->logger->warning('[UpdateOrderStatusController] Invalid uuidClient format.');
            return new JsonResponse(['error' => 'Invalid uuid format'], 400);
        }

        // Validate status
        $validStatuses = ['draft', 'pending', 'sent', 'confirmed', 'received', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            $this->logger->warning('[UpdateOrderStatusController] Invalid status value.');
            return new JsonResponse(['error' => 'Invalid status. Must be one of: ' . implode(', ', $validStatuses)], 400);
        }

        // Verify user has access to the client
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        if (!$user->getClients()->exists(function ($key, $client) use ($uuidClient) {
            return $client->getUuidClient() === $uuidClient;
        })) {
            $this->logger->warning('[UpdateOrderStatusController] User does not have access to the specified client.');
            return new JsonResponse(['error' => 'Access denied to the specified client'], 403);
        }

        try {
            // Get optional fields
            $notes = $data['notes'] ?? null;
            $cancellationReason = $data['cancellationReason'] ?? null;

            // Create request DTO
            $updateOrderStatusRequest = new UpdateOrderStatusRequest(
                $uuidClient,
                (int) $orderId,
                $status,
                $user->getId(),
                $notes,
                $cancellationReason
            );

            // Execute use case
            $response = $this->updateOrderStatusUseCase->execute($updateOrderStatusRequest);

            if ($response->getError()) {
                $statusCode = $response->getStatusCode();
                if ($statusCode === 404) {
                    return new JsonResponse(['error' => $response->getError()], 404);
                }
                return new JsonResponse(['error' => $response->getError()], $statusCode);
            }

            return new JsonResponse(['order' => $response->getOrder()], $response->getStatusCode());

        } catch (\Exception $e) {
            $this->logger->error('[UpdateOrderStatusController] Error updating order status.', ['exception' => $e]);
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
