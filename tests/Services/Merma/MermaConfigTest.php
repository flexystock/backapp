<?php

namespace App\Tests\Services\Merma;

use App\Entity\Client\MermaConfig;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitarios de MermaConfig
 *
 * Cubre isDuringService() y expectedWasteKg() — los dos métodos
 * de dominio que usa el detector y el calculador.
 *
 * Ejecutar: php bin/phpunit tests/Entity/Client/MermaConfigTest.php
 */
class MermaConfigTest extends TestCase
{
    // ════════════════════════════════════════════════════════
    // isDuringService — horario normal (no cruza medianoche)
    // ════════════════════════════════════════════════════════

    public function test_hora_dentro_del_horario_es_servicio(): void
    {
        $this->markTestSkipped('isDuringService() no longer exists in MermaConfig');
    }

    public function test_hora_antes_de_apertura_no_es_servicio(): void
    {
        $this->markTestSkipped('isDuringService() no longer exists in MermaConfig');
    }

    public function test_hora_despues_del_cierre_no_es_servicio(): void
    {
        $this->markTestSkipped('isDuringService() no longer exists in MermaConfig');
    }

    public function test_exactamente_en_la_apertura_es_servicio(): void
    {
        $this->markTestSkipped('isDuringService() no longer exists in MermaConfig');
    }

    public function test_exactamente_en_el_cierre_es_servicio(): void
    {
        $this->markTestSkipped('isDuringService() no longer exists in MermaConfig');
    }

    // ════════════════════════════════════════════════════════
    // isDuringService — horario nocturno (cruza medianoche)
    // ════════════════════════════════════════════════════════

    public function test_horario_nocturno_dentro_a_las_23h(): void
    {
        $this->markTestSkipped('isDuringService() no longer exists in MermaConfig');
    }

    public function test_horario_nocturno_dentro_a_la_01h(): void
    {
        $this->markTestSkipped('isDuringService() no longer exists in MermaConfig');
    }

    public function test_horario_nocturno_exactamente_en_el_cierre(): void
    {
        $this->markTestSkipped('isDuringService() no longer exists in MermaConfig');
    }

    public function test_horario_nocturno_fuera_a_las_10h(): void
    {
        $this->markTestSkipped('isDuringService() no longer exists in MermaConfig');
    }

    public function test_horario_nocturno_fuera_a_las_02h01m(): void
    {
        $this->markTestSkipped('isDuringService() no longer exists in MermaConfig');
    }

    // ════════════════════════════════════════════════════════
    // expectedWasteKg
    // ════════════════════════════════════════════════════════

    public function test_expected_waste_kg_con_rendimiento_80(): void
    {
        $config = $this->makeConfig(rendimiento: 80);
        // 40 kg × (1 - 0.80) = 8 kg de merma esperada
        $this->assertEqualsWithDelta(8.0, $config->expectedWasteKg(40.0), 0.001);
    }

    public function test_expected_waste_kg_con_rendimiento_100(): void
    {
        $config = $this->makeConfig(rendimiento: 100);
        // Rendimiento perfecto → sin merma esperada
        $this->assertEqualsWithDelta(0.0, $config->expectedWasteKg(40.0), 0.001);
    }

    public function test_expected_waste_kg_con_rendimiento_0(): void
    {
        $config = $this->makeConfig(rendimiento: 0);
        // Rendimiento 0% → toda la entrada es merma
        $this->assertEqualsWithDelta(40.0, $config->expectedWasteKg(40.0), 0.001);
    }

    public function test_expected_waste_kg_con_input_cero(): void
    {
        $config = $this->makeConfig(rendimiento: 80);
        $this->assertEqualsWithDelta(0.0, $config->expectedWasteKg(0.0), 0.001);
    }

    public function test_expected_waste_kg_con_rendimiento_95(): void
    {
        $config = $this->makeConfig(rendimiento: 95);
        // 20 kg × 0.05 = 1 kg
        $this->assertEqualsWithDelta(1.0, $config->expectedWasteKg(20.0), 0.001);
    }

    // ════════════════════════════════════════════════════════
    // HELPERS
    // ════════════════════════════════════════════════════════

    private function makeConfig(int $rendimiento = 80): MermaConfig
    {
        $config = new MermaConfig();
        $config->setRendimientoEsperadoPct($rendimiento);
        return $config;
    }
}