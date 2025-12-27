<?php

namespace App\Order\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Order\Application\DTO\GetAllOrdersRequest;
use App\Order\Application\DTO\GetAllOrdersResponse;
use App\Order\Application\InputPorts\GetAllOrdersUseCaseInterface;
use App\Order\Infrastructure\OutputAdapters\Repositories\OrderRepository;
use App\Order\Infrastructure\OutputAdapters\Repositories\OrderItemRepository;
use Psr\Log\LoggerInterface;

class GetAllOrdersUseCase implements GetAllOrdersUseCaseInterface
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private ClientRepositoryInterface $clientRepository;

    public function __construct(
        ClientConnectionManager $connectionManager,
        LoggerInterface $logger,
        ClientRepositoryInterface $clientRepository
    ) {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
        $this->clientRepository = $clientRepository;
    }

    public function execute(GetAllOrdersRequest $request): GetAllOrdersResponse
    {
        try {
            // Get client
            $client = $this->clientRepository->findByUuid($request->getUuidClient());
            if (!$client) {
                return new GetAllOrdersResponse(null, 'CLIENT_NOT_FOUND', 404);
            }

            // Get client's entity manager
            $em = $this->connectionManager->getEntityManager($client->getUuidClient());
            $orderRepository = new OrderRepository($em);
            $orderItemRepository = new OrderItemRepository($em);

            // Get orders based on filters
            if ($request->getStatus()) {
                $orders = $orderRepository->findByStatus($request->getStatus());
            } elseif ($request->getSupplierId()) {
                $orders = $orderRepository->findBySupplierId($request->getSupplierId());
            } else {
                $orders = $em->getRepository(\App\Entity\Client\Order::class)->findAll();
            }

            // Convert orders to array with order items
            $ordersData = [];
            foreach ($orders as $order) {
                // Get order items for this order
                $orderItems = $orderItemRepository->findByOrderId($order->getId());
                
                // Convert order items to array
                $orderItemsData = [];
                foreach ($orderItems as $item) {
                    $orderItemsData[] = [
                        'id' => $item->getId(),
                        'product_id' => $item->getProductId(),
                        'quantity' => $item->getQuantity(),
                        'unit' => $item->getUnit(),
                        'unit_price' => $item->getUnitPrice(),
                        'subtotal' => $item->getSubtotal(),
                        'notes' => $item->getNotes(),
                        'prediction_data' => $item->getPredictionData(),
                        'created_at' => $item->getCreatedAt()->format('Y-m-d H:i:s'),
                    ];
                }

                $ordersData[] = [
                    'id' => $order->getId(),
                    'order_number' => $order->getOrderNumber(),
                    'client_supplier_id' => $order->getClientSupplierId(),
                    'status' => $order->getStatus(),
                    'total_amount' => $order->getTotalAmount(),
                    'currency' => $order->getCurrency(),
                    'delivery_date' => $order->getDeliveryDate()?->format('Y-m-d'),
                    'sent_at' => $order->getSentAt()?->format('Y-m-d H:i:s'),
                    'confirmed_at' => $order->getConfirmedAt()?->format('Y-m-d H:i:s'),
                    'received_at' => $order->getReceivedAt()?->format('Y-m-d H:i:s'),
                    'cancelled_at' => $order->getCancelledAt()?->format('Y-m-d H:i:s'),
                    'email_sent_to' => $order->getEmailSentTo(),
                    'notes' => $order->getNotes(),
                    'cancellation_reason' => $order->getCancellationReason(),
                    'created_by_user_id' => $order->getCreatedByUserId(),
                    'created_at' => $order->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updated_at' => $order->getUpdatedAt()->format('Y-m-d H:i:s'),
                    'items' => $orderItemsData,
                ];
            }

            $this->logger->info('[GetAllOrdersUseCase] Orders retrieved successfully', [
                'uuidClient' => $request->getUuidClient(),
                'count' => count($ordersData),
            ]);

            return new GetAllOrdersResponse($ordersData, null, 200);

        } catch (\Exception $e) {
            $this->logger->error('[GetAllOrdersUseCase] Error retrieving orders', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return new GetAllOrdersResponse(null, 'ERROR_RETRIEVING_ORDERS', 500);
        }
    }
}
