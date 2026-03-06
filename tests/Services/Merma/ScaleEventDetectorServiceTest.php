<?php

namespace App\Tests\Services\Merma;

use App\Entity\Client\MermaConfig;
use App\Services\Merma\ScaleEventClassification;
use App\Services\Merma\ScaleEventDetectorService;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitarios de ScaleEventDetectorService
 *
 * No hay base de datos, no hay framework.
 * El servicio es puro: solo recibe datos y devuelve un resultado.
 *
 * Ejecutar: php bin/phpunit tests/Services/Merma/ScaleEventDetectorServiceTest.php
 */
class ScaleEventDetectorServiceTest extends TestCase
{
    private ScaleEventDetectorService $detector;

    protected function setUp(): void
    {
        $this->detector = new ScaleEventDetectorService();
    }

    // ════════════════════════════════════════════════════════
    // REPOSICIONES
    // ════════════════════════════════════════════════════════

    public function test_subida_de_peso_es_reposicion(): void
    {
        $config = $this->makeConfig();
        $readAt = $this->makeTime('14:00'); // dentro de horario

        $result = $this->detector->classify(
            previousWeight: 2.0,
            newWeight:      5.0,
            readAt:         $readAt,
            config:         $config,
        );

        $this->assertNotNull($result);
        $this->assertSame('reposicion', $result->type);
        $this->assertEqualsWithDelta(3.0, $result->deltaKg, 0.001);
    }

    public function test_subida_de_peso_fuera_de_horario_sigue_siendo_reposicion(): void
    {
        $config = $this->makeConfig(serviceStart: '09:00', serviceEnd: '23:00');
        $readAt = $this->makeTime('03:00'); // fuera de horario

        $result = $this->detector->classify(2.0, 5.0, $readAt, $config);

        // Reposición siempre es reposición, independientemente del horario
        $this->assertNotNull($result);
        $this->assertSame('reposicion', $result->type);
    }

    // ════════════════════════════════════════════════════════
    // CONSUMO
    // ════════════════════════════════════════════════════════

    public function test_bajada_dentro_de_horario_es_consumo(): void
    {
        $config = $this->makeConfig(serviceStart: '09:00', serviceEnd: '23:00');
        $readAt = $this->makeTime('13:30');

        $result = $this->detector->classify(5.0, 4.2, $readAt, $config);

        $this->assertNotNull($result);
        $this->assertSame('consumo', $result->type);
        $this->assertEqualsWithDelta(-0.8, $result->deltaKg, 0.001);
    }

    public function test_bajada_al_inicio_del_horario_es_consumo(): void
    {
        $config = $this->makeConfig(serviceStart: '09:00', serviceEnd: '23:00');
        $readAt = $this->makeTime('09:00'); // exactamente al inicio

        $result = $this->detector->classify(5.0, 4.5, $readAt, $config);

        $this->assertSame('consumo', $result->type);
    }

    public function test_bajada_al_final_del_horario_es_consumo(): void
    {
        $config = $this->makeConfig(serviceStart: '09:00', serviceEnd: '23:00');
        $readAt = $this->makeTime('23:00'); // exactamente al cierre

        $result = $this->detector->classify(5.0, 4.5, $readAt, $config);

        $this->assertSame('consumo', $result->type);
    }

    // ════════════════════════════════════════════════════════
    // ANOMALÍAS
    // ════════════════════════════════════════════════════════

    public function test_bajada_fuera_de_horario_es_anomalia(): void
    {
        $config = $this->makeConfig(serviceStart: '09:00', serviceEnd: '23:00');
        $readAt = $this->makeTime('03:00'); // madrugada

        $result = $this->detector->classify(5.0, 4.0, $readAt, $config);

        $this->assertNotNull($result);
        $this->assertSame('anomalia', $result->type);
        $this->assertEqualsWithDelta(-1.0, $result->deltaKg, 0.001);
    }

    public function test_bajada_antes_de_apertura_es_anomalia(): void
    {
        $config = $this->makeConfig(serviceStart: '09:00', serviceEnd: '23:00');
        $readAt = $this->makeTime('08:59');

        $result = $this->detector->classify(5.0, 4.5, $readAt, $config);

        $this->assertSame('anomalia', $result->type);
    }

    public function test_bajada_despues_del_cierre_es_anomalia(): void
    {
        $config = $this->makeConfig(serviceStart: '09:00', serviceEnd: '23:00');
        $readAt = $this->makeTime('23:01');

        $result = $this->detector->classify(5.0, 4.5, $readAt, $config);

        $this->assertSame('anomalia', $result->type);
    }

    // ════════════════════════════════════════════════════════
    // UMBRAL DE RUIDO (anomaly threshold)
    // ════════════════════════════════════════════════════════

    public function test_delta_por_debajo_del_umbral_devuelve_null(): void
    {
        // Umbral: 200g. Delta: 100g → ruido del sensor
        $config = $this->makeConfig(anomalyThresholdKg: 0.200);
        $readAt = $this->makeTime('14:00');

        $result = $this->detector->classify(5.000, 5.100, $readAt, $config);

        $this->assertNull($result, 'Un delta de 100g no debe generar evento con umbral de 200g');
    }

    public function test_delta_exactamente_en_el_umbral_genera_evento(): void
    {
        $config = $this->makeConfig(anomalyThresholdKg: 0.200);
        $readAt = $this->makeTime('14:00');

        $result = $this->detector->classify(5.000, 5.200, $readAt, $config);

        $this->assertNotNull($result, 'Un delta exactamente en el umbral debe generar evento');
    }

    public function test_delta_cero_devuelve_null(): void
    {
        $config = $this->makeConfig();
        $readAt = $this->makeTime('14:00');

        $result = $this->detector->classify(5.000, 5.000, $readAt, $config);

        $this->assertNull($result);
    }

    // ════════════════════════════════════════════════════════
    // CÁLCULO DE COSTE
    // ════════════════════════════════════════════════════════

    public function test_calcula_delta_cost_cuando_hay_precio(): void
    {
        $config = $this->makeConfig();
        $readAt = $this->makeTime('14:00');

        $result = $this->detector->classify(
            previousWeight: 5.0,
            newWeight:      3.0,
            readAt:         $readAt,
            config:         $config,
            pricePerKg:     4.50, // 4,50€/kg
        );

        $this->assertNotNull($result);
        $this->assertEqualsWithDelta(9.00, $result->deltaCost, 0.01);
        // 2 kg × 4,50€/kg = 9,00€
    }

    public function test_delta_cost_es_null_sin_precio(): void
    {
        $config = $this->makeConfig();
        $readAt = $this->makeTime('14:00');

        $result = $this->detector->classify(5.0, 3.0, $readAt, $config, pricePerKg: null);

        $this->assertNull($result->deltaCost);
    }

    public function test_delta_cost_es_siempre_positivo_en_consumos(): void
    {
        $config = $this->makeConfig();
        $readAt = $this->makeTime('14:00');

        $result = $this->detector->classify(5.0, 3.0, $readAt, $config, pricePerKg: 2.0);

        // El coste es el valor absoluto del delta × precio — nunca negativo
        $this->assertGreaterThan(0, $result->deltaCost);
    }

    // ════════════════════════════════════════════════════════
    // HORARIO NOCTURNO (cruza medianoche)
    // ════════════════════════════════════════════════════════

    public function test_horario_nocturno_que_cruza_medianoche_consumo_a_las_23h(): void
    {
        // Restaurante que abre a las 13:00 y cierra a las 02:00
        $config = $this->makeConfig(serviceStart: '13:00', serviceEnd: '02:00');
        $readAt = $this->makeTime('23:30');

        $result = $this->detector->classify(5.0, 4.0, $readAt, $config);

        $this->assertSame('consumo', $result->type);
    }

    public function test_horario_nocturno_que_cruza_medianoche_consumo_a_la_01h(): void
    {
        $config = $this->makeConfig(serviceStart: '13:00', serviceEnd: '02:00');
        $readAt = $this->makeTime('01:30');

        $result = $this->detector->classify(5.0, 4.0, $readAt, $config);

        $this->assertSame('consumo', $result->type);
    }

    public function test_horario_nocturno_anomalia_a_las_10h(): void
    {
        $config = $this->makeConfig(serviceStart: '13:00', serviceEnd: '02:00');
        $readAt = $this->makeTime('10:00'); // fuera de servicio

        $result = $this->detector->classify(5.0, 4.0, $readAt, $config);

        $this->assertSame('anomalia', $result->type);
    }

    // ════════════════════════════════════════════════════════
    // VALUE OBJECT ScaleEventClassification
    // ════════════════════════════════════════════════════════

    public function test_classification_value_object_es_inmutable(): void
    {
        $classification = new ScaleEventClassification('consumo', -1.5, 3.00);

        $this->assertSame('consumo', $classification->type);
        $this->assertSame(-1.5, $classification->deltaKg);
        $this->assertSame(3.00, $classification->deltaCost);
    }

    // ════════════════════════════════════════════════════════
    // HELPERS
    // ════════════════════════════════════════════════════════

    private function makeConfig(
        string $serviceStart      = '09:00',
        string $serviceEnd        = '23:00',
        float  $anomalyThresholdKg = 0.200,
        int    $rendimiento        = 80,
    ): MermaConfig {
        $config = new MermaConfig();
        $config->setServiceStart(new \DateTime($serviceStart));
        $config->setServiceEnd(new \DateTime($serviceEnd));
        $config->setAnomalyThresholdKg($anomalyThresholdKg);
        $config->setRendimientoEsperadoPct($rendimiento);
        return $config;
    }

    private function makeTime(string $time): \DateTimeInterface
    {
        return new \DateTime("2026-03-15 {$time}:00");
    }
}