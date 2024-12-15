<?php

namespace App\Product\Application\UseCases;

use App\Product\Application\DTO\UpdateProductRequest;
use App\Product\Application\DTO\UpdateProductResponse;
use App\Product\Application\InputPorts\UpdateProductUseCaseInterface;
use App\Product\Infrastructure\OutputAdapters\Services\ClientConnectionManager;
use Psr\Log\LoggerInterface;

class UpdateProductUseCase implements UpdateProductUseCaseInterface
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;

    public function __construct(ClientConnectionManager $connectionManager, LoggerInterface $logger)
    {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
    }

    public function execute(UpdateProductRequest $request): UpdateProductResponse
    {
        $uuidClient = $request->getUuidClient();
        $uuidProduct = $request->getUuidProduct();

        if (!$uuidClient || !$uuidProduct) {
            return new UpdateProductResponse(null, 'Missing required fields: uuid_client or uuid_product', 400);
        }

        try {
            $em = $this->connectionManager->getEntityManager($uuidClient);
            $productRepository = new \App\Product\Infrastructure\OutputAdapters\Repositories\ProductRepository($em);

            $product = $productRepository->findByUuidAndClient($uuidProduct, $uuidClient);

            if (!$product) {
                $this->logger->warning("UpdateProductUseCase: Producto '$uuidProduct' no encontrado para cliente '$uuidClient'.");

                return new UpdateProductResponse(null, 'Product not found', 404);
            }

            // Actualizar sólo los campos que no sean null
            if (null !== $request->getName()) {
                $product->setName($request->getName());
            }
            if (null !== $request->getEan()) {
                $product->setEan($request->getEan());
            }
            if (null !== $request->getWeightRange()) {
                $product->setWeightRange($request->getWeightRange());
            }
            if (null !== $request->getNameUnit1()) {
                $product->setNameUnit1($request->getNameUnit1());
            }
            if (null !== $request->getWeightUnit1()) {
                $product->setWeightUnit1($request->getWeightUnit1());
            }
            if (null !== $request->getNameUnit2()) {
                $product->setNameUnit2($request->getNameUnit2());
            }
            if (null !== $request->getWeightUnit2()) {
                $product->setWeightUnit2($request->getWeightUnit2());
            }
            $mainUnit = $request->getMainUnit();
            if (null === $mainUnit || '' === $mainUnit) {
                $product->setMainUnit('0');
            } else {
                $product->setMainUnit($mainUnit);
            }
            if (null !== $request->getTare()) {
                $product->setTare($request->getTare());
            }
            if (null !== $request->getSalePrice()) {
                $product->setSalePrice($request->getSalePrice());
            }
            if (null !== $request->getCostPrice()) {
                $product->setCostPrice($request->getCostPrice());
            }
            if (null !== $request->getOutSystemStock()) {
                $product->setOutSystemStock($request->getOutSystemStock());
            }
            if (null !== $request->getDaysAverageConsumption()) {
                $product->setDaysAverageConsumption($request->getDaysAverageConsumption());
            }
            if (null !== $request->getDaysServeOrder()) {
                $product->setDaysServeOrder($request->getDaysServeOrder());
            }

            // Actualizar uuidUserModification y datehourModification
            $product->setUuidUserModification($request->getUuidUserModification());
            $product->setDatehourModification($request->getDatehourModification());

            $em->flush();

            $productData = [
                'uuid' => $product->getUuid(),
                'name' => $product->getName(),
                // ... otros campos si lo requieres
            ];

            return new UpdateProductResponse($productData, null, 200);
        } catch (\Exception $e) {
            $this->logger->error('UpdateProductUseCase: Error updating product.', [
                'uuid_client' => $uuidClient,
                'uuid_product' => $uuidProduct,
                'exception' => $e,
            ]);

            return new UpdateProductResponse(null, 'Internal Server Error', 500);
        }
    }
}
