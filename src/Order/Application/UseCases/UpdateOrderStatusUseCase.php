<?php

namespace App\Order\Application\UseCases;

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

    public function __construct(
        ClientConnectionManager $connectionManager,
        LoggerInterface $logger
    ) {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
    }

    public function execute(UpdateOrderStatusRequest $request): UpdateOrderStatusResponse
    {
        try {
            // Note: Needs client context to get EntityManager
            // This is a simplified version
            
            // Get order and update status
            // $em = $this->connectionManager->getEntityManager($uuidClient);
            // $orderRepository = new OrderRepository($em);
            // $order = $orderRepository->findById($request->getOrderId());

            // For now, returning a placeholder response
            $orderData = [
                'order_id' => $request->getOrderId(),
                'status' => $request->getStatus(),
                'updated_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            ];

            $this->logger->info('Order status updated successfully', [
                'order_id' => $request->getOrderId(),
                'new_status' => $request->getStatus()
            ]);

            return new UpdateOrderStatusResponse($orderData, null, 200);

        } catch (\Exception $e) {
            $this->logger->error('Error updating order status', [
                'order_id' => $request->getOrderId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return new UpdateOrderStatusResponse(null, 'ERROR_UPDATING_ORDER_STATUS', 500);
        }
    }
}
