<?php

namespace App\Product\Application\UseCases;

use App\Entity\Client\Product;
use App\Product\Application\DTO\GetAllProductsRequest;
use App\Product\Application\DTO\GetProductResponse;
use App\Product\Application\InputPorts\GetAllProductsUseCaseInterface;
use App\Product\Infrastructure\OutputAdapters\Services\ClientConnectionManager;
use Psr\Log\LoggerInterface;

class GetAllProductsUseCase implements GetAllProductsUseCaseInterface
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;

    public function __construct(ClientConnectionManager $connectionManager, LoggerInterface $logger)
    {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
    }

    public function execute(GetAllProductsRequest $request): GetProductResponse
    {
        $uuidClient = $request->getUuidClient();

        try {
            // Obtener el EntityManager para el cliente
            $entityManager = $this->connectionManager->getEntityManager($uuidClient);

            // Crear una instancia del repositorio con el EntityManager del cliente
            $productRepository = new \App\Product\Infrastructure\OutputAdapters\Repositories\ProductRepository($entityManager);

            // Buscar el producto

            $products = $productRepository->findAllByUuidClient($uuidClient);

            if (!$products) {
                $this->logger->warning("GetAllProductsUseCase: no se ha encontrado ningun producto para cliente '$uuidClient'.");
                return new GetProductResponse(null, 'Product not found', 404);
            }
            // Serializar todos los productos
            $serializedProducts = array_map(function (Product $p) {
                return $this->serializeProduct($p);
            }, $products);

            // Aquí "serializedProducts" es un array de arrays, representando todos los productos
            $this->logger->info("GetProductUseCase: Productos encontrados para cliente '$uuidClient'.");

            return new GetProductResponse($serializedProducts, null, 200);
        } catch (\Exception $e) {
            $this->logger->error('GetProductUseCase: Error obteniendo los productos.', [
                'uuid_client' => $uuidClient,
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
