<?php

namespace App\Order\Application\UseCases;

use App\Entity\Client\Order;
use App\Entity\Client\OrderItem;
use App\Entity\Client\OrderHistory;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Order\Application\DTO\CreateOrderRequest;
use App\Order\Application\DTO\CreateOrderResponse;
use App\Order\Application\InputPorts\CreateOrderUseCaseInterface;
use App\Order\Infrastructure\OutputAdapters\Repositories\OrderRepository;
use App\Order\Infrastructure\OutputAdapters\Repositories\OrderItemRepository;
use App\Order\Infrastructure\OutputAdapters\Repositories\OrderHistoryRepository;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateOrderUseCase implements CreateOrderUseCaseInterface
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
     * Execute create order use case.
     * 
     * Note: This use case requires client UUID context to be passed in the request.
     * The full implementation should be completed when integrating with the TTN
     * weight monitoring system that triggers automatic orders.
     * 
     * @param CreateOrderRequest $request
     * @return CreateOrderResponse
     */
    public function execute(CreateOrderRequest $request): CreateOrderResponse
    {
        // TODO: Add uuidClient to CreateOrderRequest when implementing automatic order creation
        // This will be needed when weight from TTN falls below minimum stock threshold
        
        try {
            $this->logger->info('Order creation initiated', [
                'order_number' => $request->getOrderNumber(),
                'client_supplier_id' => $request->getClientSupplierId()
            ]);

            // TODO: Implement full order creation logic:
            // 1. Get client context: $client = $this->clientRepository->findByUuid($uuidClient)
            // 2. Get client EntityManager: $em = $this->connectionManager->getEntityManager($client->getUuidClient())
            // 3. Create OrderRepository with client EntityManager
            // 4. Create Order entity and OrderItem entities
            // 5. Create OrderHistory entry for initial status
            // 6. Calculate total amount from items
            // 7. Persist order and items
            
            throw new \RuntimeException('ORDER_CREATION_NOT_IMPLEMENTED_YET');

        } catch (\Exception $e) {
            $this->logger->error('Error creating order', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return new CreateOrderResponse(null, $e->getMessage(), 500);
        }
    }
}
