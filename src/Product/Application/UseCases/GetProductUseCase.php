<?php

// src/Product/Application/UseCases/GetProductUseCase.php

namespace App\Product\Application\UseCases;

use App\Entity\Client\Product;
use App\Product\Application\DTO\GetProductRequest;
use App\Product\Application\DTO\GetProductResponse;
use App\Product\Application\InputPorts\GetProductUseCaseInterface;
use App\Product\Infrastructure\OutputAdapters\Services\ClientConnectionManager;
use Psr\Log\LoggerInterface;

class GetProductUseCase implements GetProductUseCaseInterface
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;

    public function __construct(ClientConnectionManager $connectionManager, LoggerInterface $logger)
    {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
    }

    public function execute(GetProductRequest $request): GetProductResponse
    {
        $uuidClient = $request->getUuidClient();
        $uuidProduct = $request->getUuidProduct();

        try {
            // Obtener el EntityManager para el cliente
            $entityManager = $this->connectionManager->getEntityManager($uuidClient);

            // Crear una instancia del repositorio con el EntityManager del cliente
            $productRepository = new \App\Product\Infrastructure\OutputAdapters\Repositories\ProductRepository($entityManager);

            // Buscar el producto

            $product = $productRepository->findByUuidAndClient($uuidProduct, $uuidClient);
            // die("antes del serialize");
            if (!$product) {
                $this->logger->warning("GetProductUseCase: Producto '$uuidProduct' no encontrado para cliente '$uuidClient'.");

                return new GetProductResponse(null, 'Product not found', 404);
            }
            $serializedProduct = $this->serializeProduct($product);

            $this->logger->info("GetProductUseCase: Producto '$uuidProduct' encontrado para cliente '$uuidClient'.");

            return new GetProductResponse($serializedProduct, null, 200);
        } catch (\Exception $e) {
            $this->logger->error('GetProductUseCase: Error obteniendo el producto.', [
                'uuid_client' => $uuidClient,
                'uuid_product' => $uuidProduct,
                'exception' => $e,
            ]);

            return new GetProductResponse(null, 'Internal Server Error', 500);
        }
    }

    /**
     * Serializa la entidad Product a un array.
     */
    private function serializeProduct(Product $product): array
    {
        return [
            'uuid' => $product->getUuid(),
            'name' => $product->getName(),
            // ... otros campos según sea necesario
        ];
    }
}
