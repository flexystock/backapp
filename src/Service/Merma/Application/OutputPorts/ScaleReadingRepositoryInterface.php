<?php

namespace App\Service\Merma\Application\OutputPorts;

use App\Entity\Client\MermaConfig;
use App\Entity\Client\MermaMonthlyReport;
use App\Entity\Client\ScaleEvent;

/**
 * Lo que Merma necesita de las lecturas de balanza (tabla ya existente).
 * Contrato mínimo — no duplicamos métodos que ya tiene ScaleReadingRepository.
 */
interface ScaleReadingRepositoryInterface
{
    /**
     * Último peso registrado ANTES de un timestamp dado.
     * Necesario para calcular el delta entre lecturas.
     */
    public function findLastWeightBefore(int $scaleId, \DateTimeInterface $before): ?float;

    /**
     * Peso registrado más cercano a un timestamp dado (inicio/fin de mes).
     * Usado para calcular stock_start y stock_end en el informe mensual.
     */
    public function findWeightAt(int $scaleId, \DateTimeInterface $at): ?float;
}