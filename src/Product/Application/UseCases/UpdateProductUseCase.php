<?php

namespace App\Product\Application\UseCases;

use App\Entity\Client\ProductHistory;
use App\Product\Application\DTO\UpdateProductRequest;
use App\Product\Application\DTO\UpdateProductResponse;
use App\Product\Application\InputPorts\UpdateProductUseCaseInterface;
use App\Infrastructure\Services\ClientConnectionManager;
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

        if (!$uuidClient) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }
        if (!$uuidProduct) {
            throw new \RuntimeException('PRODUCT_NOT_FOUND');
        }

        try {
            $em = $this->connectionManager->getEntityManager($uuidClient);
            $productRepository = new \App\Product\Infrastructure\OutputAdapters\Repositories\ProductRepository($em);

            $product = $productRepository->findByUuidAndClient($uuidProduct, $uuidClient);

            if (!$product) {
                $this->logger->warning("UpdateProductUseCase: Producto '$uuidProduct' no encontrado para cliente '$uuidClient'.");

                return new UpdateProductResponse(null, 'PRODUCT_NOT_FOUND', 404);
            }
            // obtenemos campos para guardar el historial
            $beforeData = [
                'uuid' => $product->getUuid(),
                'name' => $product->getName(),
                'ean' => $product->getEan(),
                'expiration_date' => $product->getExpirationDate(),
                'perishable' => $product->getPerishable(),
                'stock' => $product->getStock(),
                'weight_range' => $product->getWeightRange(),
                'weight_unit1' => $product->getWeightUnit1(),
                'weight_unit2' => $product->getWeightUnit2(),
                'main_unit' => $product->getMainUnit(),
                'tare' => $product->getTare(),
                'sale_price' => $product->getSalePrice(),
                'cost_price' => $product->getCostPrice(),
                'out_system_stock' => $product->getOutSystemStock(),
                'days_average_consumption' => $product->getDaysAverageConsumption(),
                'days_serve_order' => $product->getDaysServeOrder(),
            ];
            $beforeJson = json_encode($beforeData);
            // Actualizar sólo los campos que no sean null
            if (null !== $request->getName()) {
                $product->setName($request->getName());
            }
            if (null !== $request->getEan()) {
                $product->setEan($request->getEan());
            }
            if (null !== $request->getExpirationDate()) {
                $product->setExpirationDate($request->getExpirationDate());
            }
            if (null !== $request->getPerishable()) {
                $product->setPerishable($request->getPerishable());
            }
            if (null !== $request->getStock()) {
                $product->setStock($request->getStock());
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
            // Después de aplicar los cambios:
            $afterData = [
                'uuid' => $product->getUuid(),
                'name' => $product->getName(),
                'ean' => $product->getEan(),
                'expiration_date' => $product->getExpirationDate(),
                'perishable' => $product->getPerishable(),
                'stock' => $product->getStock(),
                'weight_range' => $product->getWeightRange(),
                'weight_unit1' => $product->getWeightUnit1(),
                'weight_unit2' => $product->getWeightUnit2(),
                'main_unit' => $product->getMainUnit(),
                'tare' => $product->getTare(),
                'sale_price' => $product->getSalePrice(),
                'cost_price' => $product->getCostPrice(),
                'out_system_stock' => $product->getOutSystemStock(),
                'days_average_consumption' => $product->getDaysAverageConsumption(),
                'days_serve_order' => $product->getDaysServeOrder(),
            ];
            $afterJson = json_encode($afterData);

            // Crear el registro de historial
            $history = new ProductHistory();
            $history->setUuidProduct($product->getUuid());
            $history->setUuidUserModification($request->getUuidUserModification()); // o el usuario actual
            $history->setDataProductBeforeModification($beforeJson);
            $history->setDataProductAfterModification($afterJson);
            $history->setDateModification(new \DateTime());
            // Persistir el historial
            $em->persist($history);


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
