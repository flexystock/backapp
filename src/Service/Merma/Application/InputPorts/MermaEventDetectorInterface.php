<?php

namespace App\Service\Merma\Application\InputPorts;

use App\Service\Merma\Application\DTO\MermaReportDTO;
use App\Service\Merma\Application\DTO\MermaSummaryDTO;
use App\Service\Merma\Application\DTO\ScaleEventResultDTO;
use App\Service\Merma\Application\DTO\ScaleReadingDTO;

/**
 * Contrato que expone el detector de eventos al InputAdapter (webhook TTN).
 * El controller/handler TTN llama a este interface — nunca al UseCase directamente.
 */
interface MermaEventDetectorInterface
{
    /**
     * Procesa una nueva lectura de peso y, si corresponde, genera un ScaleEvent.
     * Retorna null si la lectura no genera evento (ruido, primera lectura, etc.).
     */
    public function detect(ScaleReadingDTO $reading): ?ScaleEventResultDTO;
}