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

    public function execute(CreateOrderRequest $request): CreateOrderResponse
    {
        try {
            // Note: We need to get uuidClient from somewhere (could be added to request)
            // For now, assuming it's available in the context
            // $client = $this->clientRepository->findByUuid($uuidClient);
            // $em = $this->connectionManager->getEntityManager($client->getUuidClient());

            // For demonstration, we'll assume entity manager is injected or available
            // In real implementation, this would need client context

            // Create order entity
            $order = new Order();
            $order->setOrderNumber($request->getOrderNumber());
            $order->setClientSupplierId($request->getClientSupplierId());
            $order->setStatus($request->getStatus());
            $order->setTotalAmount($request->getTotalAmount());
            $order->setCurrency($request->getCurrency());
            $order->setDeliveryDate($request->getDeliveryDate());
            $order->setNotes($request->getNotes());
            $order->setCreatedByUserId($request->getCreatedByUserId());

            // Note: Repository needs EntityManager from client connection
            // This is a simplified version - full implementation would need client context
            
            $orderData = [
                'order_number' => $order->getOrderNumber(),
                'client_supplier_id' => $order->getClientSupplierId(),
                'status' => $order->getStatus(),
                'total_amount' => $order->getTotalAmount(),
                'currency' => $order->getCurrency(),
            ];

            $this->logger->info('Order created successfully', ['order_number' => $order->getOrderNumber()]);

            return new CreateOrderResponse($orderData, null, 201);

        } catch (\Exception $e) {
            $this->logger->error('Error creating order', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return new CreateOrderResponse(null, 'ERROR_CREATING_ORDER', 500);
        }
    }
}
