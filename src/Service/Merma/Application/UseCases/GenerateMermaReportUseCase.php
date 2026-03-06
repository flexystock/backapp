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
use App\Service\Merma\Application\OutputPorts\MermaProductRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\ScaleEventRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\ScaleReadingRepositoryInterface;
use App\Service\Merma\MermaReportGeneratorService;
use Psr\Log\LoggerInterface;

// ═══════════════════════════════════════════════════════
// GenerateMermaReportUseCase
// ═══════════════════════════════════════════════════════

final class GenerateMermaReportUseCase implements MermaReportGeneratorInterface
{
    public function __construct(
        private readonly MermaReportGeneratorService           $generator,
        private readonly ScaleEventRepositoryInterface         $eventRepo,
        private readonly ScaleReadingRepositoryInterface       $readingRepo,
        private readonly MermaConfigRepositoryInterface        $configRepo,
        private readonly MermaMonthlyReportRepositoryInterface $reportRepo,
        private readonly MermaProductRepositoryInterface       $productRepo,
        private readonly MermaNotifierInterface                $notifier,
        private readonly LoggerInterface                       $logger,
    ) {}

    public function generateForAllScales(?\DateTimeInterface $month = null): int
    {
        $targetMonth = $month ?? new \DateTime('first day of last month 00:00:00');

        $this->logger->info('MermaReport: iniciando generación para {m}', [
            'month' => $targetMonth->format('Y-m'),
        ]);

        $pairs     = $this->eventRepo->findActiveScaleProductPairsForMonth($targetMonth);
        $generated = 0;

        foreach ($pairs as $pair) {
            try {
                $report = $this->generateForScale(
                    $pair['scaleId'],
                    $pair['productId'],
                    $targetMonth
                );
                if ($report !== null) {
                    $generated++;
                }
            } catch (\Throwable $e) {
                $this->logger->error('MermaReport error scale={s} product={p}: {msg}', [
                    's'   => $pair['scaleId'],
                    'p'   => $pair['productId'],
                    'msg' => $e->getMessage(),
                ]);
            }
        }

        $this->logger->info('MermaReport: {n} informes generados', ['n' => $generated]);
        return $generated;
    }

    public function generateForScale(int $scaleId, int $productId, \DateTimeInterface $month): ?MermaReportDTO
    {
        $monthStart = new \DateTime($month->format('Y-m-01') . ' 00:00:00');
        $monthEnd   = new \DateTime($month->format('Y-m-t')  . ' 23:59:59');

        // Evitar duplicados
        if ($this->reportRepo->findForPeriod($scaleId, $productId, $monthStart) !== null) {
            return null;
        }

        // Delegar el cálculo al Service de dominio
        $result = $this->generator->calculate(
            scaleId:    $scaleId,
            productId:  $productId,
            monthStart: $monthStart,
            monthEnd:   $monthEnd,
            eventRepo:  $this->eventRepo,
            readingRepo: $this->readingRepo,
            configRepo:  $this->configRepo,
        );

        if ($result === null) {
            return null; // Sin actividad ese mes
        }

        $product = $this->productRepo->findById($productId); // ver nota abajo
        if ($product !== null && $product->getCostPrice() > 0) {
            $pricePerKg = $product->getCostPrice() / $product->getConversionFactor();
            $result     = $this->generator->applyPricing($result, $pricePerKg);
        }

        // Persistir
        $report = new MermaMonthlyReport();
        $report->setScaleId($scaleId)
            ->setProductId($productId)
            ->setPeriodMonth($monthStart)
            ->setInputKg($result->inputKg)
            ->setConsumedKg($result->consumedKg)
            ->setAnomalyKg($result->anomalyKg)
            ->setStockStartKg($result->stockStartKg)
            ->setStockEndKg($result->stockEndKg)
            ->setExpectedWasteKg($result->expectedWasteKg)
            ->setActualWasteKg($result->actualWasteKg)
            ->setWasteCostEuros($result->wasteCostEuros)
            ->setWastePct($result->wastePct)
            ->setSavedVsBaseline($result->savedVsBaseline);

        $this->reportRepo->save($report);

        // Enviar email al cliente
        $this->notifier->sendMonthlyReport($report);

        $dto = $this->toDTO($report);

        $this->logger->info('MermaReport guardado: scale={s} period={m} waste={w}% saved={e}€', [
            's' => $scaleId,
            'm' => $monthStart->format('Y-m'),
            'w' => $result->wastePct,
            'e' => $result->savedVsBaseline,
        ]);

        return $dto;
    }

    private function toDTO(MermaMonthlyReport $r): MermaReportDTO
    {
        return new MermaReportDTO(
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
        );
    }
}