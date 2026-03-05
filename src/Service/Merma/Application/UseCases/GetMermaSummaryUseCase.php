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
    ) {}

    public function execute(GetMermaSummaryRequest $request): MermaSummaryDTO
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if ($client === null) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $em        = $this->connectionManager->getEntityManager($client->getUuidClient());
        $eventRepo = new ClientScaleEventRepository($em);

        $start = new \DateTime('first day of this month 00:00:00');
        $end   = new \DateTime('now');

        $inputKg    = $eventRepo->sumDeltaByType($request->getScaleId(), $request->getProductId(), ScaleEvent::TYPE_REPOSICION, $start, $end);
        $consumedKg = abs($eventRepo->sumDeltaByType($request->getScaleId(), $request->getProductId(), ScaleEvent::TYPE_CONSUMO, $start, $end));
        $anomalyKg  = abs($eventRepo->sumDeltaByType($request->getScaleId(), $request->getProductId(), ScaleEvent::TYPE_ANOMALIA, $start, $end));

        $estimatedWasteKg  = max(0.0, round($inputKg - $consumedKg, 3));
        $estimatedWastePct = $inputKg > 0 ? round(($estimatedWasteKg / $inputKg) * 100, 1) : 0.0;
        $pendingCount      = $eventRepo->countPendingAnomalies($request->getScaleId(), $request->getProductId());

        $this->logger->info('MermaSummary retrieved', [
            'scaleId'   => $request->getScaleId(),
            'productId' => $request->getProductId(),
        ]);

        return new MermaSummaryDTO(
            inputKg:               $inputKg,
            consumedKg:            $consumedKg,
            anomalyKg:             $anomalyKg,
            estimatedWasteKg:      $estimatedWasteKg,
            estimatedWastePct:     $estimatedWastePct,
            estimatedCostEuros:    0.0,
            pendingAnomaliesCount: $pendingCount,
        );
    }
}
