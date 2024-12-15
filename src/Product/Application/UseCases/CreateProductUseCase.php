<?php

namespace App\Product\Application\UseCases;

use App\Product\Application\DTO\CreateProductRequest;
use App\Product\Application\DTO\CreateProductResponse;
use App\Product\Application\InputPorts\CreateProductUseCaseInterface;
use App\Product\Application\OutputPorts\Repositories\ProductRepositoryInterface;
use App\Product\Infrastructure\OutputAdapters\Repositories\ProductRepository;
use App\Product\Infrastructure\OutputAdapters\Services\ClientConnectionManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

class CreateProductUseCase implements CreateProductUseCaseInterface
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;

    // El repositorio debe ser un ProductRepositoryInterface
    public function __construct(ClientConnectionManager $connectionManager, LoggerInterface $logger)
    {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
    }

    public function execute(CreateProductRequest $request): CreateProductResponse
    {
        $uuidClient = $request->getUuidClient();
        $name = $request->getName();
        $uuidUserCreation = $request->getUuidUserCreation();
        $datehourCreation = $request->getDatehourCreation();

        if (!$uuidClient || !$name) {
            return new CreateProductResponse(null, 'Missing required fields', 400);
        }

        try {
            // Obtener el EntityManager para el cliente
            $em = $this->connectionManager->getEntityManager($uuidClient);

            // Crear el repositorio
            $productRepository = new ProductRepository($em);

            // Crear la entidad Product (en src/Entity/Client/Product.php)
            $product = new \App\Entity\Client\Product();
            $product->setUuid(Uuid::v4()->toRfc4122());
            $product->setName($name);
            $product->setUuidUserCreation($uuidUserCreation);
            $product->setDatehourCreation($datehourCreation);


            // Guardar el producto usando el repositorio
            $productRepository->save($product);

            // Serializar el producto para la respuesta
            $productData = [
                'uuid' => $product->getUuid(),
                'name' => $product->getName(),
            ];

            return new CreateProductResponse($productData, null, 200);
        } catch (\Exception $e) {
            $this->logger->error('Error creating product', ['exception' => $e, 'uuid_client' => $uuidClient]);

            return new CreateProductResponse(null, 'Internal Server Error', 500);
        }
    }
}
