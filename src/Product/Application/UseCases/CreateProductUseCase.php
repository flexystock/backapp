<?php

namespace App\Product\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\Product;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Product\Application\DTO\CreateProductRequest;
use App\Product\Application\DTO\CreateProductResponse;
use App\Product\Application\InputPorts\CreateProductUseCaseInterface;
use App\Product\Application\OutputPorts\Repositories\ProductRepositoryInterface;
use App\Product\Infrastructure\OutputAdapters\Repositories\ProductRepository;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

class CreateProductUseCase implements CreateProductUseCaseInterface
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private ProductRepositoryInterface $productRepository;
    private ClientRepositoryInterface $clientRepository;
    private UserRepositoryInterface $userRepository;

    // El repositorio debe ser un ProductRepositoryInterface
    public function __construct(ClientConnectionManager $connectionManager, LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        ClientRepositoryInterface $clientRepository,
        UserRepositoryInterface $userRepository)
    {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
        $this->productRepository = $productRepository;
        $this->clientRepository = $clientRepository;
        $this->userRepository = $userRepository;
    }

    public function execute(CreateProductRequest $request): CreateProductResponse
    {
        // 1) Comprobar si el cliente existe
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if (!$client) {
            // Lanza \RuntimeException('CLIENT_NOT_FOUND')
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        // 3) Crear la entidad Product
        $em = $this->connectionManager->getEntityManager($client->getUuidClient());

        // Crear el repositorio
        $productRepository = new ProductRepository($em);

        // Crear la entidad Product (en src/Entity/Client/Product.php)
        $product = new Product();
        $product->setUuid(Uuid::v4()->toRfc4122());
        $product->setName($request->getName());
        $product->setUuidUserCreation($request->getUuidUserCreation());
        $product->setDatehourCreation($request->getDatehourCreation());
        $product->setMainUnit($request->getMainUnit());
        $product->setTare($request->getTare());
        $product->setSalePrice($request->getSalePrice());
        $product->setCostPrice($request->getCostPrice());
        $product->setOutSystemStock($request->getOutSystemStock());
        $product->setDaysAverageConsumption($request->getDaysAverageConsumption());
        $product->setDaysServeOrder($request->getDaysServeOrder());
        $product->setEan($request->getEan());
        $product->setExpirationDate($request->getExpirationDate());
        $product->setPerishable($request->getPerishable());
        $product->setStock($request->getStock());
        $product->setWeightRange($request->getWeightRange());
        $product->setNameUnit1($request->getNameUnit1());
        $product->setWeightUnit1($request->getWeightUnit1());
        $product->setNameUnit2($request->getNameUnit2());
        $product->setWeightUnit2($request->getWeightUnit2());

        // 4) Persistir
        $productRepository->save($product);

        $productData = [
            'uuid' => $product->getUuid(),
            'name' => $product->getName(),
        ];

        return new CreateProductResponse($productData, null, 200);
    }
}
