<?php

namespace App\WeightAnalytics\Application\UseCases;

use App\Infrastructure\Services\ClientConnectionManager;
use App\WeightAnalytics\Application\DTO\GetProductWeightSummaryRequest;
use App\WeightAnalytics\Application\DTO\GetProductWeightSummaryResponse;
use App\WeightAnalytics\Application\InputPorts\GetProductWeightSummaryUseCaseInterface;
use App\WeightAnalytics\Application\OutputPorts\Repositories\WeightsLogRepositoryInterface;
use App\WeightAnalytics\Infrastructure\OutputAdapters\Repositories\WeightsLogRepository;
use DateTime;
use Psr\Log\LoggerInterface;

class GetProductWeightSummaryUseCase implements GetProductWeightSummaryUseCaseInterface
{
    private ClientConnectionManager $clientConnectionManager;
    private WeightsLogRepositoryInterface $weightsLogRepository;
    private LoggerInterface $logger;

    public function __construct(
        ClientConnectionManager $clientConnectionManager,
        WeightsLogRepositoryInterface $weightsLogRepository,
        LoggerInterface $logger
    ) {
        $this->clientConnectionManager = $clientConnectionManager;
        $this->weightsLogRepository = $weightsLogRepository;
        $this->logger = $logger;
    }

    public function execute(GetProductWeightSummaryRequest $request): GetProductWeightSummaryResponse
    {
        $uuidClient = $request->getUuidClient();
        $productId = $request->getProductId();
        $from = $request->getFrom();
        $to = $request->getTo();

        if (!$uuidClient || !$productId) {
            return new GetProductWeightSummaryResponse(null, 'uuidClient and productId are required', 400);
        }

        try {
            $em = $this->clientConnectionManager->getEntityManager($uuidClient);
            $repo = new WeightsLogRepository($em);

            // Convertir strings a DateTime, si se han pasado
            $fromDateObj = $from ? new \DateTime($from) : null;
            $toDateObj = $to ? new \DateTime($to) : null;
            file_put_contents('/appdata/www/var/doctrine/proxies/prueba_runtime.txt', 'Hello from PHP at '.date('c'));
            $this->logger->info('DEBUG CLIENT DB', [
                'current_user' => get_current_user(),
            ]);

            $summary = $repo->getProductWeightSummary($productId, $fromDateObj, $toDateObj);

            // Obtener información del producto para unidades
            $product = $em->getRepository(\App\Entity\Client\Product::class)->find($productId);
            
            if (!$product) {
                $this->logger->warning("GetProductWeightSummaryUseCase: Producto no encontrado con ID '$productId'.");
                return new GetProductWeightSummaryResponse(null, 'Product not found', 404);
            }

            $unitInfo = $this->getProductUnitInfo($product);

            $summaryArray = [];
            foreach ($summary as $item) {
                $realWeight = $item->getRealWeight();
                $conversionFactor = $unitInfo['conversion_factor'];
                
                // Calcular stock en la unidad principal del producto
                $stockInUnits = $conversionFactor > 0 ? $realWeight / $conversionFactor : 0;
                
                $summaryArray[] = [
                    'id' => $item->getId(),
                    'scale_id' => $item->getScaleId(),
                    'product_id' => $item->getProductId(),
                    'date' => $item->getDate()->format('Y-m-d H:i:s'),
                    'stock' => round($stockInUnits),
                    'adjust_weight' => $item->getAdjustWeight(),
                    'charge_percentage' => $item->getChargePercentage(),
                    'voltage' => $item->getVoltage(),
                ];
            }

            // Agregar información de unidades a la respuesta
            $responseData = [
                'summary' => $summaryArray,
                'unit_info' => $unitInfo,
            ];

            return new GetProductWeightSummaryResponse($responseData, null, 200);
        } catch (\Exception $e) {
            // Loguear para debug
            $this->logger->error('Error en GetProductWeightSummaryUseCase: '.$e->getMessage());

            return new GetProductWeightSummaryResponse(null, 'INTERNAL_SERVER_ERROR', 500);
        }
    }

    /**
     * Obtiene la información de unidades del producto.
     */
    private function getProductUnitInfo(\App\Entity\Client\Product $product): array
    {
        $mainUnit = (int) $product->getMainUnit();

        switch ($mainUnit) {
            case 1:
                return [
                    'main_unit' => $mainUnit,
                    'unit_name' => $product->getNameUnit1(),
                    'conversion_factor' => $product->getWeightUnit1() ?? 1,
                ];
            case 2:
                return [
                    'main_unit' => $mainUnit,
                    'unit_name' => $product->getNameUnit2(),
                    'conversion_factor' => $product->getWeightUnit2() ?? 1,
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
}
