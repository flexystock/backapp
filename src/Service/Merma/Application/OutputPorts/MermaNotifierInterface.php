<?php

namespace App\Service\Merma\Application\OutputPorts;

use App\Entity\Client\MermaConfig;
use App\Entity\Client\MermaMonthlyReport;
use App\Entity\Client\ScaleEvent;

/**
 * Contrato de notificaciones — desacopla Merma del sistema de email concreto.
 */
interface MermaNotifierInterface
{
    /** Alerta inmediata cuando se detecta una anomalía fuera de horario */
    public function sendAnomalyAlert(
        ScaleEvent $event,
        array $recipientEmails = [],
        string $unitLabel = 'kg',
        float $conversionFactor = 1.0,
    ): void;

    /** Informe mensual completo al cliente */
    public function sendMonthlyReport(MermaMonthlyReport $report): void;
}
