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

    /**
     * Execute update order status use case.
     * 
     * Note: This use case requires client UUID context to access the client database.
     * Full implementation needed when integrating with order management UI.
     * 
     * @param UpdateOrderStatusRequest $request
     * @return UpdateOrderStatusResponse
     */
    public function execute(UpdateOrderStatusRequest $request): UpdateOrderStatusResponse
    {
        // TODO: Add uuidClient to UpdateOrderStatusRequest for client context
        
        try {
            $this->logger->info('Order status update initiated', [
                'order_id' => $request->getOrderId(),
                'new_status' => $request->getStatus()
            ]);

            // TODO: Implement full order status update logic:
            // 1. Get client context: $client = $this->clientRepository->findByUuid($uuidClient)
            // 2. Get client EntityManager: $em = $this->connectionManager->getEntityManager($client->getUuidClient())
            // 3. Create OrderRepository and OrderHistoryRepository with client EntityManager
            // 4. Find order by ID
            // 5. Validate status transition
            // 6. Update order status and set appropriate timestamp (sentAt, confirmedAt, etc.)
            // 7. Create OrderHistory entry to track the status change
            // 8. If status is 'cancelled', set cancellation reason
            // 9. Persist changes
            
            throw new \RuntimeException('ORDER_STATUS_UPDATE_NOT_IMPLEMENTED_YET');

        } catch (\Exception $e) {
            $this->logger->error('Error updating order status', [
                'order_id' => $request->getOrderId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return new UpdateOrderStatusResponse(null, $e->getMessage(), 500);
        }
    }
}
