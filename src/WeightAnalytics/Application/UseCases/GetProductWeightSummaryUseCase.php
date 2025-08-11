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

            $summaryArray = [];
            foreach ($summary as $item) {
                $summaryArray[] = [
                    'id' => $item->getId(),
                    'scale_id' => $item->getScaleId(),
                    'product_id' => $item->getProductId(),
                    'date' => $item->getDate()->format('Y-m-d H:i:s'),
                    'real_weight' => $item->getRealWeight(),
                    'adjust_weight' => $item->getAdjustWeight(),
                    'charge_percentage' => $item->getChargePercentage(),
                    'voltage' => $item->getVoltage(),
                ];
            }

            return new GetProductWeightSummaryResponse($summaryArray, null, 200);
        } catch (\Exception $e) {
            // Loguear para debug
            $this->logger->error('Error en GetProductWeightSummaryUseCase: '.$e->getMessage());

            return new GetProductWeightSummaryResponse(null, 'INTERNAL_SERVER_ERROR', 500);
        }
    }
}
