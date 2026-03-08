<?php

namespace App\Service\Merma\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\ScaleEvent;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Service\Merma\Application\DTO\GetMermaSummaryRequest;
use App\Service\Merma\Application\DTO\MermaSummaryDTO;
use App\Service\Merma\Application\InputPorts\GetMermaSummaryUseCaseInterface;
use App\Service\Merma\Infrastructure\OutputAdapters\Repositories\ClientScaleEventRepository;
use Psr\Log\LoggerInterface;

final class GetMermaSummaryUseCase implements GetMermaSummaryUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager   $connectionManager,
        private readonly LoggerInterface           $logger,
    ) {
    }

    public function execute(GetMermaSummaryRequest $request): MermaSummaryDTO
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if ($client === null) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $em        = $this->connectionManager->getEntityManager($client->getUuidClient());
        $eventRepo = new ClientScaleEventRepository($em);

        // ── Producto y precio por kg ─────────────────────────────────────────────
        $product    = $em->getRepository(\App\Entity\Client\Product::class)->find($request->getProductId());
        $pricePerKg = 0.0;
        if ($product !== null && $product->getCostPrice() > 0 && $product->getConversionFactor() > 0) {
            $pricePerKg = $product->getCostPrice() / $product->getConversionFactor();
        }

        // ── Rendimiento esperado (de MermaConfig) ────────────────────────────────
        $mermaConfig    = $em->getRepository(\App\Entity\Client\MermaConfig::class)
            ->findOneBy(['product' => $request->getProductId()]);
        $rendimientoPct = $mermaConfig !== null ? $mermaConfig->getRendimientoEsperadoPct() : 80;

        // ── Mes anterior ─────────────────────────────────────────────────────────
        $prevStart      = new \DateTime('first day of last month 00:00:00');
        $prevEnd        = new \DateTime('last day of last month 23:59:59');
        $prevInputKg    = $eventRepo->sumDeltaByType($request->getScaleId(), $request->getProductId(), ScaleEvent::TYPE_REPOSICION, $prevStart, $prevEnd);
        $prevConsumedKg = abs($eventRepo->sumDeltaByType($request->getScaleId(), $request->getProductId(), ScaleEvent::TYPE_CONSUMO, $prevStart, $prevEnd));
        $prevWasteKg    = max(0.0, round($prevInputKg - $prevConsumedKg, 3));
        $prevWastePct   = $prevInputKg > 0 ? round(($prevWasteKg / $prevInputKg) * 100, 1) : 0.0;
        $prevCostEuros  = $pricePerKg > 0 && $prevWasteKg > 0 ? round($prevWasteKg * $pricePerKg, 2) : 0.0;

        // ── Mes actual ───────────────────────────────────────────────────────────
        $start      = new \DateTime('first day of this month 00:00:00');
        $end        = new \DateTime('now');
        $inputKg    = $eventRepo->sumDeltaByType($request->getScaleId(), $request->getProductId(), ScaleEvent::TYPE_REPOSICION, $start, $end);
        $consumedKg = abs($eventRepo->sumDeltaByType($request->getScaleId(), $request->getProductId(), ScaleEvent::TYPE_CONSUMO, $start, $end));
        $anomalyKg  = abs($eventRepo->sumDeltaByType($request->getScaleId(), $request->getProductId(), ScaleEvent::TYPE_ANOMALIA, $start, $end));

        // ── Stock actual en báscula ───────────────────────────────────────────────
        $lastLog = $em->createQuery(
            'SELECT w FROM App\Entity\Client\WeightsLog w
             WHERE w.scale = :scaleId
             ORDER BY w.date DESC'
        )
            ->setParameter('scaleId', $request->getScaleId())
            ->setMaxResults(1)
            ->getOneOrNullResult();

        $currentStockKg = $lastLog !== null ? (float) $lastLog->getRealWeight() : 0.0;

        // ── Cálculos finales ─────────────────────────────────────────────────────
        $estimatedWasteKg   = max(0.0, round($inputKg - $consumedKg - $currentStockKg, 3));
        $estimatedWastePct  = $inputKg > 0 ? round(($estimatedWasteKg / $inputKg) * 100, 1) : 0.0;
        $estimatedCostEuros = $pricePerKg > 0 && $estimatedWasteKg > 0 ? round($estimatedWasteKg * $pricePerKg, 2) : 0.0;
        $pendingCount       = $eventRepo->countPendingAnomalies($request->getScaleId(), $request->getProductId());

        $this->logger->info('MermaSummary retrieved', [
            'scaleId'         => $request->getScaleId(),
            'productId'       => $request->getProductId(),
            'currentStockKg'  => $currentStockKg,
            'estimatedWaste'  => $estimatedWasteKg,
            'rendimientoPct'  => $rendimientoPct,
        ]);

        return new MermaSummaryDTO(
            inputKg:               $inputKg,
            consumedKg:            $consumedKg,
            anomalyKg:             $anomalyKg,
            estimatedWasteKg:      $estimatedWasteKg,
            estimatedWastePct:     $estimatedWastePct,
            estimatedCostEuros:    $estimatedCostEuros,
            pendingAnomaliesCount: $pendingCount,
            prevMonthWastePct:     $prevWastePct,
            prevMonthCostEuros:    $prevCostEuros,
            rendimientoEsperadoPct: $rendimientoPct,
        );
    }
}

