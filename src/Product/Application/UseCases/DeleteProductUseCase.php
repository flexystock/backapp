<?php

namespace App\Product\Application\UseCases;

use App\Entity\Main\User;
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

    public function execute(DeleteProductRequest $request, User $user): DeleteProductResponse
    {
        $uuidClient = $request->getUuidClient();
        $uuidProduct = $request->getUuidProduct();

        if (!$uuidClient) {
            // Lanza \RuntimeException('CLIENT_NOT_FOUND')
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }
        if (!$uuidProduct) {
            // Lanza \RuntimeException('PRODUCT_NOT_FOUND')
            throw new \RuntimeException('PRODUCT_NOT_FOUND');
        }
        // Validar acceso del usuario al cliente
        if (!$user->getClients()->exists(function ($key, $client) use ($uuidClient) {
            return $client->getUuidClient() === $uuidClient;
        })) {
            throw new \RuntimeException('ACCESS_DENIED');
        }

        try {
            $em = $this->connectionManager->getEntityManager($uuidClient);
            $productRepository = new \App\Product\Infrastructure\OutputAdapters\Repositories\ProductRepository($em);

            $product = $productRepository->findByUuidAndClient($uuidProduct, $uuidClient);

            if (!$product) {
                $this->logger->warning("DeleteProductUseCase: Producto '$uuidProduct' no encontrado para cliente '$uuidClient'.");
                // Lanza \RuntimeException('PRODUCT_NOT_FOUND')
                throw new \RuntimeException('PRODUCT_NOT_FOUND');
            }

            // antes de liminar el producto hay que poneren la scale (Balanza) el campo product_id = 'free'
            // y despues elminar el rpoducto
            $scaleRepository = new \App\Scales\Infrastructure\OutputAdapters\Repositories\ScalesRepository($em);
            $scale = $scaleRepository->findOneByProductId($product->getId());

            if ($scale) {
                $scale->setProduct(null);
                $scaleRepository->save($scale);
            }
            $productRepository->remove($product);

            return new DeleteProductResponse('PRODUCT_DELETED_SUCCESSFULLY', null, 200);
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
