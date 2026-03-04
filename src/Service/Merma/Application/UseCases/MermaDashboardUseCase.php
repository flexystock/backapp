<?php

namespace App\Service\Merma\Application\UseCases;

use App\Entity\Client\MermaMonthlyReport;
use App\Entity\Client\ScaleEvent;
use App\Service\Merma\Application\DTO\MermaReportDTO;
use App\Service\Merma\Application\DTO\MermaSummaryDTO;
use App\Service\Merma\Application\InputPorts\MermaDashboardInterface;
use App\Service\Merma\Application\InputPorts\MermaReportGeneratorInterface;
use App\Service\Merma\Application\OutputPorts\MermaConfigRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\MermaMonthlyReportRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\MermaNotifierInterface;
use App\Service\Merma\Application\OutputPorts\ScaleEventRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\ScaleReadingRepositoryInterface;
use App\Service\Merma\MermaReportGeneratorService;
use Psr\Log\LoggerInterface;

// ═══════════════════════════════════════════════════════
// MermaDashboardUseCase
// ═══════════════════════════════════════════════════════

final class MermaDashboardUseCase implements MermaDashboardInterface
{
    public function __construct(
        private readonly ScaleEventRepositoryInterface         $eventRepo,
        private readonly MermaMonthlyReportRepositoryInterface $reportRepo,
    ) {}

    public function getSummary(int $scaleId, int $productId): MermaSummaryDTO
    {
        $start = new \DateTime('first day of this month 00:00:00');
        $end   = new \DateTime('now');

        $inputKg    = $this->eventRepo->sumDeltaByType($scaleId, $productId, ScaleEvent::TYPE_REPOSICION, $start, $end);
        $consumedKg = abs($this->eventRepo->sumDeltaByType($scaleId, $productId, ScaleEvent::TYPE_CONSUMO, $start, $end));
        $anomalyKg  = abs($this->eventRepo->sumDeltaByType($scaleId, $productId, ScaleEvent::TYPE_ANOMALIA, $start, $end));

        $estimatedWasteKg  = max(0.0, round($inputKg - $consumedKg, 3));
        $estimatedWastePct = $inputKg > 0 ? round(($estimatedWasteKg / $inputKg) * 100, 1) : 0.0;

        // El coste lo calcula el repository directamente (tiene acceso al precio del producto)
        $pendingCount = $this->eventRepo->countPendingAnomalies($scaleId, $productId);

        return new MermaSummaryDTO(
            inputKg:               $inputKg,
            consumedKg:            $consumedKg,
            anomalyKg:             $anomalyKg,
            estimatedWasteKg:      $estimatedWasteKg,
            estimatedWastePct:     $estimatedWastePct,
            estimatedCostEuros:    0.0, // El controller lo enriquece con el precio si lo necesita
            pendingAnomaliesCount: $pendingCount,
        );
    }

    public function confirmAnomaly(int $eventId): void
    {
        $event = $this->eventRepo->findById($eventId);
        if ($event === null) {
            throw new \InvalidArgumentException("ScaleEvent {$eventId} no encontrado");
        }
        $event->setIsConfirmed(true)->setConfirmedAt(new \DateTime());
        $this->eventRepo->save($event);
    }

    public function discardAnomaly(int $eventId): void
    {
        $event = $this->eventRepo->findById($eventId);
        if ($event === null) {
            throw new \InvalidArgumentException("ScaleEvent {$eventId} no encontrado");
        }
        $event->setIsConfirmed(false)->setConfirmedAt(new \DateTime());
        $this->eventRepo->save($event);
    }

    public function getMonthlyHistory(int $scaleId, int $productId, int $limit = 12): array
    {
        $reports = $this->reportRepo->findHistoryForScale($scaleId, $productId, $limit);

        return array_map(fn(MermaMonthlyReport $r) => new MermaReportDTO(
            reportId:        $r->getId(),
            productId:       $r->getProductId(),
            scaleId:         $r->getScaleId(),
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