<?php

namespace App\Product\Application\UseCases;

use App\Entity\Client\Product;
use App\Product\Application\DTO\GetInfoToDashboardMainRequest;
use App\Product\Application\DTO\GetInfoToDashboardMainResponse;
use App\Product\Application\InputPorts\GetInfoToDashboardMainUseCaseInterface;
use App\Product\Infrastructure\OutputAdapters\Repositories\ProductRepository;
use App\Product\Infrastructure\OutputAdapters\Repositories\WeightsLogRepository;
use App\Product\Infrastructure\OutputAdapters\Services\ClientConnectionManager;
use Psr\Log\LoggerInterface;

class GetInfoToDashboardMainUseCase implements GetInfoToDashboardMainUseCaseInterface
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;

    public function __construct(ClientConnectionManager $connectionManager, LoggerInterface $logger)
    {
        $this->connectionManager = $connectionManager;
        $this->logger = $logger;
    }

    public function execute(GetInfoToDashboardMainRequest $request): GetInfoToDashboardMainResponse
    {
        $uuidClient = $request->getUuidClient();

        try {
            // Obtener el EntityManager para el cliente
            $entityManager = $this->connectionManager->getEntityManager($uuidClient);

            // Instanciar los repositorios necesarios
            $productRepository = new ProductRepository($entityManager);
            $weightsLogRepository = new WeightsLogRepository($entityManager);

            // Buscar todos los productos del cliente
            $products = $productRepository->findAllByUuidClient($uuidClient);

            if (!$products) {
                $this->logger->warning("GetAllProductsUseCase: no se ha encontrado ningún producto para el cliente '$uuidClient'.");

                return new GetInfoToDashboardMainResponse(null, 'Product not found', 404);
            }

            // Serializamos cada producto utilizando un método privado
            $serializedProducts = array_map(function (Product $product) use ($weightsLogRepository) {
                return $this->serializeProductWithWeights($product, $weightsLogRepository);
            }, $products);

            $this->logger->info("GetProductUseCase: Productos encontrados para el cliente '$uuidClient'.");

            return new GetInfoToDashboardMainResponse($serializedProducts, null, 200);
        } catch (\Exception $e) {
            $this->logger->error('GetProductUseCase: Error obteniendo los productos.', [
                'uuid_client' => $uuidClient,
                'exception' => $e,
            ]);

            return new GetInfoToDashboardMainResponse(null, 'Internal Server Error', 500);
        }
    }

    /**
     * Serializa un producto incluyendo el cálculo del peso real y la conversión a la unidad definida.
     */
    private function serializeProductWithWeights(Product $product, WeightsLogRepository $weightsLogRepository): array
    {
        $productId = $product->getId();
        $stock = $product->getStock();

        // Obtener la suma total del peso real en Kg
        $realWeightSum = $weightsLogRepository->getLatestTotalRealWeightByProduct($productId) ?? 0;

        // Calcular el porcentaje del peso real respecto al stock
        $percentage = $stock > 0 ? ($realWeightSum / $stock) * 100 : 0;

        // Obtener información de conversión (nombre y factor)
        $conversionInfo = $this->getConversionInfo($product);
        $mainUnit = $conversionInfo['main_unit'];
        $unitName = $conversionInfo['unit_name'];
        $conversionFactor = $conversionInfo['conversion_factor'];

        // Calcular el stock y el peso en la unidad definida
        $stockInUnits = $conversionFactor > 0 ? round($stock / $conversionFactor, 2) : 0;
        $realWeightSumInUnits = $conversionFactor > 0 ? round($realWeightSum / $conversionFactor, 2) : 0;

        return [
            'uuid' => $product->getUuid(),
            'name' => $product->getName(),
            'stock_kg' => $stock,                 // Stock en Kg
            'stock_in_units' => $stockInUnits,          // Stock en la unidad definida
            'real_weight_sum_kg' => $realWeightSum,         // Peso acumulado en Kg
            'real_weight_sum_in_units' => $realWeightSumInUnits,    // Peso acumulado en la unidad definida
            'percentage' => round($percentage, 2).'%',
            'unit' => [
                'main_unit' => $mainUnit,
                'unit_name' => $unitName,
                'conversion_factor' => $conversionFactor,       // Factor de conversión a Kg
            ],
        ];
    }

    /**
     * Obtiene la información de conversión para un producto.
     *
     * Se utiliza el campo main_unit para determinar la unidad:
     * - 0: Se consideran Kg (por defecto).
     * - 1: Se utiliza name_unit1 y weight_unit1.
     * - 2: Se utiliza name_unit2 y weight_unit2.
     */
    private function getConversionInfo(Product $product): array
    {
        // Convertir a entero para evitar confusiones (en caso de que se guarde como string)
        $mainUnit = (int) $product->getMainUnit();

        switch ($mainUnit) {
            case 1:
                return [
                    'main_unit' => $mainUnit,
                    'unit_name' => $product->getNameUnit1(),
                    'conversion_factor' => $product->getWeightUnit1(),
                ];
            case 2:
                return [
                    'main_unit' => $mainUnit,
                    'unit_name' => $product->getNameUnit2(),
                    'conversion_factor' => $product->getWeightUnit2(),
                ];
            case 0:
            default:
                return [
                    'main_unit' => 0,
                    'unit_name' => 'Kg',
                    'conversion_factor' => 1,
                ];
        }
    }

    /**
     * Método opcional para serializar otros campos del producto si se requiere.
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
