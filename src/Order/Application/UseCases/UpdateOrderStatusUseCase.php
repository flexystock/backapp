<?php

namespace App\Order\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\OrderHistory;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Order\Application\DTO\UpdateOrderStatusRequest;
use App\Order\Application\DTO\UpdateOrderStatusResponse;
use App\Order\Application\InputPorts\UpdateOrderStatusUseCaseInterface;
use App\Order\Infrastructure\OutputAdapters\Repositories\OrderRepository;
use App\Order\Infrastructure\OutputAdapters\Repositories\OrderHistoryRepository;
use Psr\Log\LoggerInterface;

class UpdateOrderStatusUseCase implements UpdateOrderStatusUseCaseInterface
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

    /**
     * Execute update order status use case.
     * 
     * @param UpdateOrderStatusRequest $request
     * @return UpdateOrderStatusResponse
     */
    public function execute(UpdateOrderStatusRequest $request): UpdateOrderStatusResponse
    {
        try {
            $this->logger->info('[UpdateOrderStatus] Order status update initiated', [
                'order_id' => $request->getOrderId(),
                'new_status' => $request->getStatus()
            ]);

            // Get client
            $client = $this->clientRepository->findByUuid($request->getUuidClient());
            if (!$client) {
                return new UpdateOrderStatusResponse(null, 'CLIENT_NOT_FOUND', 404);
            }

            // Get client EntityManager
            $em = $this->connectionManager->getEntityManager($client->getUuidClient());
            $orderRepository = new OrderRepository($em);
            $orderHistoryRepository = new OrderHistoryRepository($em);

            // Find order by ID
            $order = $orderRepository->findById($request->getOrderId());
            if (!$order) {
                return new UpdateOrderStatusResponse(null, 'ORDER_NOT_FOUND', 404);
            }

            // Store old status for history
            $oldStatus = $order->getStatus();

            // Update order status
            $order->setStatus($request->getStatus());

            // Set appropriate timestamp based on new status
            $now = new \DateTime();
            switch ($request->getStatus()) {
                case 'sent':
                    $order->setSentAt($now);
                    break;
                case 'confirmed':
                    $order->setConfirmedAt($now);
                    break;
                case 'received':
                    $order->setReceivedAt($now);
                    break;
                case 'cancelled':
                    $order->setCancelledAt($now);
                    if ($request->getCancellationReason()) {
                        $order->setCancellationReason($request->getCancellationReason());
                    }
                    break;
            }

            // Save order
            $orderRepository->save($order);

            // Create OrderHistory entry
            $orderHistory = new OrderHistory();
            $orderHistory->setOrderId($order->getId());
            $orderHistory->setStatusFrom($oldStatus);
            $orderHistory->setStatusTo($request->getStatus());
            $orderHistory->setChangedByUserId($request->getChangedByUserId());
            $orderHistory->setNotes($request->getNotes());

            $orderHistoryRepository->save($orderHistory);

            // Prepare response data
            $orderData = [
                'id' => $order->getId(),
                'order_number' => $order->getOrderNumber(),
                'status' => $order->getStatus(),
                'updated_at' => $order->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];

            $this->logger->info('[UpdateOrderStatus] Order status updated successfully', [
                'order_id' => $order->getId(),
                'old_status' => $oldStatus,
                'new_status' => $request->getStatus()
            ]);

            return new UpdateOrderStatusResponse($orderData, null, 200);

        } catch (\Exception $e) {
            $this->logger->error('[UpdateOrderStatus] Error updating order status', [
                'order_id' => $request->getOrderId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return new UpdateOrderStatusResponse(null, 'ERROR_UPDATING_ORDER_STATUS', 500);
        }
    }
}
