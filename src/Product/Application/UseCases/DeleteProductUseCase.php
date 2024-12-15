<?php

namespace App\Product\Application\UseCases;

use App\Product\Application\DTO\DeleteProductRequest;
use App\Product\Application\DTO\DeleteProductResponse;
use App\Product\Application\InputPorts\DeleteProductUseCaseInterface;
use App\Product\Infrastructure\OutputAdapters\Services\ClientConnectionManager;
use Psr\Log\LoggerInterface;

class DeleteProductUseCase implements DeleteProductUseCaseInterface
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;

    public function __construct(ClientConnectionManager $connectionManager, LoggerInterface $logger)
    {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
    }

    public function execute(DeleteProductRequest $request): DeleteProductResponse
    {
        $uuidClient = $request->getUuidClient();
        $uuidProduct = $request->getUuidProduct();

        if (!$uuidClient || !$uuidProduct) {
            return new DeleteProductResponse(null, 'Missing required fields: uuid_client or uuid_product', 400);
        }

        try {
            $em = $this->connectionManager->getEntityManager($uuidClient);
            $productRepository = new \App\Product\Infrastructure\OutputAdapters\Repositories\ProductRepository($em);

            $product = $productRepository->findByUuidAndClient($uuidProduct, $uuidClient);

            if (!$product) {
                $this->logger->warning("DeleteProductUseCase: Producto '$uuidProduct' no encontrado para cliente '$uuidClient'.");
                return new DeleteProductResponse(null, 'Product not found', 404);
            }

            // Aquí puedes agregar un método en el repositorio para remover el producto, similar a save().
            // Por ejemplo, en ProductRepository: public function remove(Product $product): void { ... }
            $productRepository->remove($product);

            return new DeleteProductResponse('Product deleted successfully', null, 200);
        } catch (\Exception $e) {
            $this->logger->error('DeleteProductUseCase: Error deleting product.', [
                'uuid_client' => $uuidClient,
                'uuid_product' => $uuidProduct,
                'exception' => $e,
            ]);

            return new DeleteProductResponse(null, 'Internal Server Error', 500);
        }
    }
}
