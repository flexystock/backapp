<?php

namespace App\Service\Merma\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\MermaMonthlyReport;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Service\Merma\Application\DTO\GetMermaMonthlyHistoryRequest;
use App\Service\Merma\Application\DTO\MermaReportDTO;
use App\Service\Merma\Application\InputPorts\GetMermaMonthlyHistoryUseCaseInterface;
use App\Service\Merma\Infrastructure\OutputAdapters\Repositories\ClientMermaMonthlyReportRepository;
use Psr\Log\LoggerInterface;

final class GetMermaMonthlyHistoryUseCase implements GetMermaMonthlyHistoryUseCaseInterface
{
    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager   $connectionManager,
        private readonly LoggerInterface           $logger,
    ) {}

    /**
     * @return MermaReportDTO[]
     */
    public function execute(GetMermaMonthlyHistoryRequest $request): array
    {
        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if ($client === null) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $em         = $this->connectionManager->getEntityManager($client->getUuidClient());
        $reportRepo = new ClientMermaMonthlyReportRepository($em);

        $reports = $reportRepo->findHistoryForScale(
            $request->getScaleId(),
            $request->getProductId(),
            $request->getLimit()
        );

        $this->logger->info('MermaMonthlyHistory retrieved', [
            'scaleId'   => $request->getScaleId(),
            'productId' => $request->getProductId(),
            'count'     => count($reports),
        ]);

        return array_map(fn(MermaMonthlyReport $r) => new MermaReportDTO(
            reportId:        $r->getId(),
            productId:       $r->getProduct()->getId(),
            scaleId:         $r->getScale()->getId(),
            periodLabel:     $r->getPeriodLabel(),
            inputKg:         $r->getInputKg(),
            consumedKg:      $r->getConsumedKg(),
            anomalyKg:       $r->getAnomalyKg(),
            actualWasteKg:   $r->getActualWasteKg(),
            wastePct:        $r->getWastePct(),
            wasteCostEuros:  $r->getWasteCostEuros(),
            savedVsBaseline: $r->getSavedVsBaseline(),
            status:          $r->getWasteStatus(),
        ), $reports);
    }
}
