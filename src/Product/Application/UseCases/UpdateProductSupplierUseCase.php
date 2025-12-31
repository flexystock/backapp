<?php

namespace App\Product\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Entity\Client\ProductSupplier;
use App\Product\Application\DTO\UpdateProductSupplierRequest;
use App\Product\Application\DTO\UpdateProductSupplierResponse;
use App\Product\Application\InputPorts\UpdateProductSupplierUseCaseInterface;
use App\Product\Application\OutputPorts\Repositories\ProductSupplierRepositoryInterface;
use App\Order\Application\OutputPorts\Repositories\ClientSupplierRepositoryInterface;
use App\Product\Application\OutputPorts\Repositories\ProductRepositoryInterface;
use Psr\Log\LoggerInterface;

class UpdateProductSupplierUseCase implements UpdateProductSupplierUseCaseInterface
{
    private ClientRepositoryInterface $clientRepository;
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;

    public function __construct(
        ClientRepositoryInterface $clientRepository,
        ClientConnectionManager $connectionManager,
        LoggerInterface $logger
    ) {
        $this->clientRepository = $clientRepository;
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
    }

    public function execute(UpdateProductSupplierRequest $request): UpdateProductSupplierResponse
    {
        $this->logger->info('[UpdateProductSupplier] Starting product-supplier assignment', [
            'uuidClient' => $request->getUuidClient(),
            'productId' => $request->getProductId(),
            'clientSupplierId' => $request->getClientSupplierId(),
        ]);

        // Get client
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if (!$client) {
            $this->logger->error('[UpdateProductSupplier] Client not found', [
                'uuidClient' => $request->getUuidClient(),
            ]);
            throw new \RuntimeException('Client not found');
        }

        // Get client EntityManager
        $clientEm = $this->connectionManager->getEntityManager($client->getUuidClient());

        // Create repositories with client EM
        $productSupplierRepository = new \App\Product\Infrastructure\OutputAdapters\Repositories\ProductSupplierRepository($clientEm);
        $clientSupplierRepository = new \App\Order\Infrastructure\OutputAdapters\Repositories\ClientSupplierRepository($clientEm);
        $productRepository = new \App\Product\Infrastructure\OutputAdapters\Repositories\ProductRepository($clientEm);

        // Validate product exists
        $product = $productRepository->findById($request->getProductId());
        if (!$product) {
            $this->logger->error('[UpdateProductSupplier] Product not found', [
                'productId' => $request->getProductId(),
            ]);
            throw new \RuntimeException('Product not found');
        }

        // Validate client supplier exists
        $clientSupplier = $clientSupplierRepository->findById($request->getClientSupplierId());
        if (!$clientSupplier) {
            $this->logger->error('[UpdateProductSupplier] Client supplier not found', [
                'clientSupplierId' => $request->getClientSupplierId(),
            ]);
            throw new \RuntimeException('Client supplier not found');
        }

        // Check if product-supplier relationship already exists
        $productSupplier = $productSupplierRepository->findByProductIdAndClientSupplierId(
            $request->getProductId(),
            $request->getClientSupplierId()
        );

        if (!$productSupplier) {
            // Create new product-supplier relationship
            $productSupplier = new ProductSupplier();
            $productSupplier->setProductId($request->getProductId());
            $productSupplier->setClientSupplierId($request->getClientSupplierId());
            
            $this->logger->info('[UpdateProductSupplier] Creating new product-supplier relationship');
        } else {
            $this->logger->info('[UpdateProductSupplier] Updating existing product-supplier relationship', [
                'productSupplierId' => $productSupplier->getId(),
            ]);
        }

        // If setting as preferred, unset other preferred suppliers for this product
        if ($request->isPreferred()) {
            $preferredSupplier = $productSupplierRepository->findPreferredByProductId($request->getProductId());
            if ($preferredSupplier && $preferredSupplier->getId() !== ($productSupplier->getId() ?? 0)) {
                $preferredSupplier->setIsPreferred(false);
                $productSupplierRepository->save($preferredSupplier);
                
                $this->logger->info('[UpdateProductSupplier] Unset previous preferred supplier', [
                    'previousPreferredId' => $preferredSupplier->getId(),
                ]);
            }
        }

        // Update product supplier fields
        $productSupplier->setIsPreferred($request->isPreferred());
        $productSupplier->setProductCode($request->getProductCode());
        $productSupplier->setUnitPrice($request->getUnitPrice());
        $productSupplier->setMinOrderQuantity($request->getMinOrderQuantity());
        $productSupplier->setDeliveryDays($request->getDeliveryDays());
        $productSupplier->setNotes($request->getNotes());

        // Save
        $productSupplierRepository->save($productSupplier);

        $this->logger->info('[UpdateProductSupplier] Product-supplier assignment completed successfully', [
            'productSupplierId' => $productSupplier->getId(),
        ]);

        // Build response
        $responseData = [
            'id' => $productSupplier->getId(),
            'product_id' => $productSupplier->getProductId(),
            'client_supplier_id' => $productSupplier->getClientSupplierId(),
            'is_preferred' => $productSupplier->isPreferred(),
            'product_code' => $productSupplier->getProductCode(),
            'unit_price' => $productSupplier->getUnitPrice(),
            'min_order_quantity' => $productSupplier->getMinOrderQuantity(),
            'delivery_days' => $productSupplier->getDeliveryDays(),
            'notes' => $productSupplier->getNotes(),
            'created_at' => $productSupplier->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $productSupplier->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];

        return new UpdateProductSupplierResponse($responseData);
    }
}
