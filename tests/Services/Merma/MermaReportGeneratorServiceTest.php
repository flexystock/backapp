<?php

namespace App\Tests\Services\Merma;

use App\Entity\Client\MermaConfig;
use App\Service\Merma\Application\OutputPorts\MermaConfigRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\ScaleEventRepositoryInterface;
use App\Service\Merma\Application\OutputPorts\ScaleReadingRepositoryInterface;
use App\Service\Merma\MermaCalculationResult;
use App\Service\Merma\MermaReportGeneratorService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitarios de MermaReportGeneratorService
 *
 * Todos los repositorios son mocks — sin base de datos, sin framework.
 *
 * Ejecutar: php bin/phpunit tests/Services/Merma/MermaReportGeneratorServiceTest.php
 */
class MermaReportGeneratorServiceTest extends TestCase
{
    private MermaReportGeneratorService $generator;

    private ScaleEventRepositoryInterface&MockObject   $eventRepo;
    private ScaleReadingRepositoryInterface&MockObject $readingRepo;
    private MermaConfigRepositoryInterface&MockObject  $configRepo;

    private const SCALE_ID   = 42;
    private const PRODUCT_ID = 7;

    protected function setUp(): void
    {
        $this->generator   = new MermaReportGeneratorService();
        $this->eventRepo   = $this->createMock(ScaleEventRepositoryInterface::class);
        $this->readingRepo = $this->createMock(ScaleReadingRepositoryInterface::class);
        $this->configRepo  = $this->createMock(MermaConfigRepositoryInterface::class);
    }

    // ════════════════════════════════════════════════════════
    // CÁLCULO BASE
    // ════════════════════════════════════════════════════════

    public function test_calculo_basico_de_merma(): void
    {
        // Escenario:
        //   Stock inicio:  5 kg
        //   Reposiciones: 40 kg  (compramos 40 kg durante el mes)
        //   Consumo:      38 kg  (lo que se usó en horario de servicio)
        //   Stock fin:     4 kg  (lo que queda en la balanza al final del mes)
        //
        //   Merma real = 5 + 40 - 38 - 4 = 3 kg
        //   Merma %    = 3/40 × 100 = 7.5%

        $this->mockEventRepo(inputKg: 40, consumedKg: -38, anomalyKg: 0);
        $this->mockReadingRepo(stockStart: 5.0, stockEnd: 4.0);
        $this->mockConfigRepo(rendimiento: 80);

        $result = $this->calculate();

        $this->assertNotNull($result);
        $this->assertEqualsWithDelta(40.0, $result->inputKg, 0.001);
        $this->assertEqualsWithDelta(38.0, $result->consumedKg, 0.001);
        $this->assertEqualsWithDelta(3.0,  $result->actualWasteKg, 0.001);
        $this->assertEqualsWithDelta(7.5,  $result->wastePct, 0.01);
    }

    public function test_merma_cero_cuando_todo_cuadra(): void
    {
        // stock_inicio + input - consumo - stock_fin = 0
        $this->mockEventRepo(inputKg: 30, consumedKg: -28, anomalyKg: 0);
        $this->mockReadingRepo(stockStart: 5.0, stockEnd: 7.0);
        $this->mockConfigRepo();

        $result = $this->calculate();

        $this->assertEqualsWithDelta(0.0, $result->actualWasteKg, 0.001);
        $this->assertEqualsWithDelta(0.0, $result->wastePct, 0.01);
    }

    public function test_merma_nunca_es_negativa(): void
    {
        // Caso con imprecisión del sensor que podría dar negativo
        // stock_inicio(5) + input(10) - consumo(14) - stock_fin(3) = -2 → debe ser 0
        $this->mockEventRepo(inputKg: 10, consumedKg: -14, anomalyKg: 0);
        $this->mockReadingRepo(stockStart: 5.0, stockEnd: 3.0);
        $this->mockConfigRepo();

        $result = $this->calculate();

        $this->assertGreaterThanOrEqual(0.0, $result->actualWasteKg,
            'La merma nunca debe ser negativa — protege contra imprecisión del sensor');
    }

    // ════════════════════════════════════════════════════════
    // ANOMALÍAS INCLUIDAS EN EL CÁLCULO
    // ════════════════════════════════════════════════════════

    public function test_anomalias_se_incluyen_en_el_informe(): void
    {
        // Las anomalías (sustracciones) contribuyen a la merma real
        // porque redujeron el stock pero no están en "consumo en horario"
        $this->mockEventRepo(inputKg: 40, consumedKg: -36, anomalyKg: -2);
        $this->mockReadingRepo(stockStart: 5.0, stockEnd: 4.0);
        $this->mockConfigRepo();

        $result = $this->calculate();

        $this->assertEqualsWithDelta(2.0, $result->anomalyKg, 0.001);
        // La merma incluye las anomalías: 5 + 40 - 36 - 4 = 5 kg
        $this->assertEqualsWithDelta(5.0, $result->actualWasteKg, 0.001);
    }

    // ════════════════════════════════════════════════════════
    // MERMA ESPERADA (rendimiento configurado)
    // ════════════════════════════════════════════════════════

    public function test_merma_esperada_con_rendimiento_80_pct(): void
    {
        // Rendimiento 80% → de 40 kg comprados, 8 kg de merma operativa esperada
        $this->mockEventRepo(inputKg: 40, consumedKg: -32, anomalyKg: 0);
        $this->mockReadingRepo(stockStart: 5.0, stockEnd: 5.0);
        $this->mockConfigRepo(rendimiento: 80);

        $result = $this->calculate();

        // expected_waste = 40 × (1 - 0.80) = 8 kg
        $this->assertEqualsWithDelta(8.0, $result->expectedWasteKg, 0.001);
    }

    public function test_merma_esperada_con_rendimiento_95_pct(): void
    {
        $this->mockEventRepo(inputKg: 20, consumedKg: -18, anomalyKg: 0);
        $this->mockReadingRepo(stockStart: 2.0, stockEnd: 2.0);
        $this->mockConfigRepo(rendimiento: 95);

        $result = $this->calculate();

        // expected_waste = 20 × (1 - 0.95) = 1 kg
        $this->assertEqualsWithDelta(1.0, $result->expectedWasteKg, 0.001);
    }

    public function test_merma_esperada_usa_fallback_20pct_sin_config(): void
    {
        $this->mockEventRepo(inputKg: 30, consumedKg: -27, anomalyKg: 0);
        $this->mockReadingRepo(stockStart: 3.0, stockEnd: 3.0);

        // Sin config → configRepo devuelve null
        $this->configRepo
            ->method('findByProductId')
            ->willReturn(null);

        $result = $this->calculate();

        // Fallback 20%: expected_waste = 30 × 0.20 = 6 kg
        $this->assertEqualsWithDelta(6.0, $result->expectedWasteKg, 0.001);
    }

    // ════════════════════════════════════════════════════════
    // AHORRO VS. BASELINE DEL SECTOR
    // ════════════════════════════════════════════════════════

    public function test_ahorro_vs_baseline_cuando_merma_es_menor_que_8pct(): void
    {
        // Merma real: 3 kg sobre 40 kg = 7.5%
        // Sector baseline: 8% → 3.2 kg
        // Ahorro en kg: 3.2 - 3.0 = 0.2 kg
        $this->mockEventRepo(inputKg: 40, consumedKg: -38, anomalyKg: 0);
        $this->mockReadingRepo(stockStart: 5.0, stockEnd: 4.0);
        $this->mockConfigRepo();

        $result = $this->calculate();

        $this->assertEqualsWithDelta(0.2, $result->savedKg, 0.01);
    }

    public function test_ahorro_es_cero_cuando_merma_supera_baseline(): void
    {
        // Merma real 10% > baseline 8% → no hay ahorro
        $this->mockEventRepo(inputKg: 40, consumedKg: -33, anomalyKg: 0);
        $this->mockReadingRepo(stockStart: 3.0, stockEnd: 6.0);
        $this->mockConfigRepo();

        $result = $this->calculate();

        $this->assertGreaterThan(8.0, $result->wastePct,
            'El test requiere merma > 8% para validar el caso sin ahorro');
        $this->assertEqualsWithDelta(0.0, $result->savedKg, 0.001,
            'El ahorro debe ser 0 cuando la merma supera el baseline del sector');
    }

    // ════════════════════════════════════════════════════════
    // applyPricing — enriquecimiento con precio
    // ════════════════════════════════════════════════════════

    public function test_apply_pricing_calcula_coste_y_ahorro_en_euros(): void
    {
        // Preparamos un resultado de cálculo base
        $this->mockEventRepo(inputKg: 40, consumedKg: -38, anomalyKg: 0);
        $this->mockReadingRepo(stockStart: 5.0, stockEnd: 4.0);
        $this->mockConfigRepo();

        $result = $this->calculate(); // actualWasteKg=3, savedKg=0.2

        // Enriquecemos con precio 5€/kg
        $priced = $this->generator->applyPricing($result, pricePerKg: 5.0);

        $this->assertEqualsWithDelta(15.0, $priced->wasteCostEuros, 0.01);
        // 3 kg × 5€/kg = 15€
        $this->assertEqualsWithDelta(1.0, $priced->savedVsBaseline, 0.01);
        // 0.2 kg × 5€/kg = 1€ de ahorro
    }

    public function test_apply_pricing_preserva_todos_los_campos_originales(): void
    {
        $this->mockEventRepo(inputKg: 20, consumedKg: -18, anomalyKg: -1);
        $this->mockReadingRepo(stockStart: 2.0, stockEnd: 1.0);
        $this->mockConfigRepo();

        $result = $this->calculate();
        $priced = $this->generator->applyPricing($result, pricePerKg: 3.0);

        // Todos los campos del cálculo original se preservan
        $this->assertSame($result->inputKg,       $priced->inputKg);
        $this->assertSame($result->consumedKg,    $priced->consumedKg);
        $this->assertSame($result->anomalyKg,     $priced->anomalyKg);
        $this->assertSame($result->actualWasteKg, $priced->actualWasteKg);
        $this->assertSame($result->wastePct,      $priced->wastePct);
    }

    // ════════════════════════════════════════════════════════
    // SIN ACTIVIDAD
    // ════════════════════════════════════════════════════════

    public function test_retorna_null_si_no_hay_actividad_en_el_mes(): void
    {
        // Sin reposiciones ni consumo → no generar informe vacío
        $this->mockEventRepo(inputKg: 0, consumedKg: 0, anomalyKg: 0);
        $this->mockReadingRepo(stockStart: 5.0, stockEnd: 5.0);
        $this->mockConfigRepo();

        $result = $this->calculate();

        $this->assertNull($result,
            'No debe generarse informe si el mes no tuvo actividad');
    }

    // ════════════════════════════════════════════════════════
    // VALUE OBJECT MermaCalculationResult
    // ════════════════════════════════════════════════════════

    public function test_merma_calculation_result_es_inmutable(): void
    {
        $result = new MermaCalculationResult(
            inputKg:         40.0,
            consumedKg:      38.0,
            anomalyKg:        0.0,
            stockStartKg:     5.0,
            stockEndKg:       4.0,
            expectedWasteKg:  8.0,
            actualWasteKg:    3.0,
            wastePct:         7.5,
            savedKg:          0.2,
            wasteCostEuros:  15.0,
            savedVsBaseline:  1.0,
        );

        $this->assertSame(40.0, $result->inputKg);
        $this->assertSame(3.0,  $result->actualWasteKg);
        $this->assertSame(7.5,  $result->wastePct);
    }

    // ════════════════════════════════════════════════════════
    // HELPERS
    // ════════════════════════════════════════════════════════

    private function calculate(): ?MermaCalculationResult
    {
        return $this->generator->calculate(
            scaleId:     self::SCALE_ID,
            productId:   self::PRODUCT_ID,
            monthStart:  new \DateTime('2026-03-01 00:00:00'),
            monthEnd:    new \DateTime('2026-03-31 23:59:59'),
            eventRepo:   $this->eventRepo,
            readingRepo: $this->readingRepo,
            configRepo:  $this->configRepo,
        );
    }

    private function mockEventRepo(float $inputKg, float $consumedKg, float $anomalyKg): void
    {
        $this->eventRepo
            ->method('sumDeltaByType')
            ->willReturnCallback(function (int $scaleId, int $productId, string $type) use ($inputKg, $consumedKg, $anomalyKg): float {
                return match ($type) {
                    'reposicion' => $inputKg,
                    'consumo'    => $consumedKg,
                    'anomalia'   => $anomalyKg,
                    default      => 0.0,
                };
            });
    }

    private function mockReadingRepo(float $stockStart, float $stockEnd): void
    {
        $this->readingRepo
            ->method('findWeightAt')
            ->willReturnOnConsecutiveCalls($stockStart, $stockEnd);
    }

    private function mockConfigRepo(int $rendimiento = 80): void
    {
        $config = new MermaConfig();
        $config->setRendimientoEsperadoPct($rendimiento);

        $this->configRepo
            ->method('findByProductId')
            ->willReturn($config);
    }
}
