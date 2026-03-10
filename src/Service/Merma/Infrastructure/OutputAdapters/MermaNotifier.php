<?php

namespace App\Service\Merma\Infrastructure\OutputAdapters;

use App\Entity\Client\MermaMonthlyReport;
use App\Entity\Client\ScaleEvent;
use App\Service\Merma\Application\OutputPorts\MermaNotifierInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class MermaNotifier implements MermaNotifierInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $logger,
        private readonly string          $fromEmail,
        private readonly string          $appUrl,
    ) {}

    // ── Alerta inmediata de anomalía ─────────────────────────────────────────
    // Los destinatarios se pasan desde HandleTtnUplinkUseCase, que ya tiene
    // acceso al cliente via TtnAlarmNotificationService.
    // Si no hay destinatarios configurados, se loguea y se ignora.

    public function sendAnomalyAlert(
        ScaleEvent $event,
        array $recipientEmails = [],
        string $unitLabel = 'kg',
        float $conversionFactor = 1.0,
    ): void {
        if (empty($recipientEmails)) {
            $this->logger->info('[MermaNotifier] sendAnomalyAlert: sin destinatarios, omitiendo.', [
                'scaleId'   => $event->getScale()->getId(),
                'productId' => $event->getProduct()->getId(),
            ]);
            return;
        }

        $scaleName   = $event->getScale()->getEndDeviceId();
        $productName = $event->getProduct()->getName();

        $toUnits = function(float $kg) use ($conversionFactor, $unitLabel): string {
            if ($conversionFactor > 0 && $conversionFactor != 1.0) {
                return round($kg / $conversionFactor) . ' ' . $unitLabel;
            }
            return number_format($kg, 3) . ' kg';
        };

        $subject = sprintf('⚠️ Anomalía detectada — %s', $productName);

        $htmlBody = sprintf('
        <div style="font-family:Arial,sans-serif;max-width:600px;margin:0 auto;background:#fff;border:1px solid #e0d4c8;border-radius:8px;overflow:hidden">
            <div style="background:#8a6d52;padding:20px 24px">
                <h2 style="color:#fff;margin:0">⚠️ Anomalía de merma detectada</h2>
            </div>
            <div style="padding:24px">
                <p style="color:#555">Se ha detectado una variación inesperada de peso fuera del horario de servicio configurado.</p>
                <table style="width:100%%;border-collapse:collapse;margin:16px 0">
                    <tr style="background:#f9f5f1">
                        <td style="padding:10px 14px;color:#7a5c44;font-weight:bold;width:40%%">Báscula</td>
                        <td style="padding:10px 14px">%s</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 14px;color:#7a5c44;font-weight:bold">Producto</td>
                        <td style="padding:10px 14px">%s</td>
                    </tr>
                    <tr style="background:#f9f5f1">
                        <td style="padding:10px 14px;color:#7a5c44;font-weight:bold">Cantidad anterior</td>
                        <td style="padding:10px 14px">%s</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 14px;color:#7a5c44;font-weight:bold">Cantidad actual</td>
                        <td style="padding:10px 14px">%s</td>
                    </tr>
                    <tr style="background:#f9f5f1">
                        <td style="padding:10px 14px;color:#7a5c44;font-weight:bold">Variación</td>
                        <td style="padding:10px 14px;color:#c0392b;font-weight:bold">-%s</td>
                    </tr>
                    <tr>
                        <td style="padding:10px 14px;color:#7a5c44;font-weight:bold">Coste estimado</td>
                        <td style="padding:10px 14px;color:#c0392b;font-weight:bold">%.2f €</td>
                    </tr>
                    <tr style="background:#f9f5f1">
                        <td style="padding:10px 14px;color:#7a5c44;font-weight:bold">Detectado</td>
                        <td style="padding:10px 14px">%s</td>
                    </tr>
                </table>
                <div style="margin-top:24px;text-align:center">
                    <a href="%s" style="background:#8a6d52;color:#fff;padding:12px 28px;border-radius:6px;text-decoration:none;font-weight:bold">
                        Revisar en FlexyStock →
                    </a>
                </div>
                <p style="margin-top:24px;font-size:12px;color:#aaa;text-align:center">
                    Puedes confirmar o descartar esta anomalía desde el panel de Gestión de Merma.
                </p>
            </div>
        </div>',
            $scaleName,
            $productName,
            $toUnits($event->getWeightBefore()),
            $toUnits($event->getWeightAfter()),
            $toUnits(abs($event->getDeltaKg())),
            abs($event->getDeltaCost()),
            $event->getDetectedAt()->format('d/m/Y H:i:s'),
            $this->appUrl
        );

        $textBody = sprintf(
            "Anomalía detectada — %s\n\nBáscula: %s\nProducto: %s\nCantidad anterior: %s\nCantidad actual: %s\nVariación: -%s\nCoste estimado: %.2f €\nDetectado: %s\n\nRevisar en: %s",
            $productName,
            $scaleName,
            $productName,
            $toUnits($event->getWeightBefore()),
            $toUnits($event->getWeightAfter()),
            $toUnits(abs($event->getDeltaKg())),
            abs($event->getDeltaCost()),
            $event->getDetectedAt()->format('d/m/Y H:i:s'),
            $this->appUrl
        );

        foreach ($recipientEmails as $recipient) {
            try {
                $this->mailer->send(
                    (new Email())
                        ->from($this->fromEmail)
                        ->to($recipient)
                        ->subject($subject)
                        ->text($textBody)
                        ->html($htmlBody)
                );
                $this->logger->info('[MermaNotifier] Alerta de anomalía enviada.', [
                    'recipient' => $recipient,
                    'scaleId'   => $event->getScale()->getId(),
                ]);
            } catch (TransportExceptionInterface $e) {
                $this->logger->error('[MermaNotifier] Error enviando alerta de anomalía.', [
                    'recipient' => $recipient,
                    'error'     => $e->getMessage(),
                ]);
            }
        }
    }

    // ── Informe mensual ───────────────────────────────────────────────────────

    public function sendMonthlyReport(MermaMonthlyReport $report, array $recipientEmails = []): void
    {
        if (empty($recipientEmails)) {
            $this->logger->info('[MermaNotifier] sendMonthlyReport: sin destinatarios, omitiendo.', [
                'reportId' => $report->getId(),
            ]);
            return;
        }

        $subject = sprintf(
            'Informe mensual de merma — %s',
            $report->getPeriodLabel()
        );

        $htmlBody = sprintf(
            '<h2>Informe mensual de merma</h2>
        <p>Periodo: <strong>%s</strong></p>
        <table cellpadding="6" style="border-collapse:collapse">
            <tr><td><strong>Stock inicial</strong></td><td>%.3f kg</td></tr>
            <tr><td><strong>Entradas</strong></td><td>%.3f kg</td></tr>
            <tr><td><strong>Consumo</strong></td><td>%.3f kg</td></tr>
            <tr><td><strong>Stock final</strong></td><td>%.3f kg</td></tr>
            <tr><td><strong>Merma real</strong></td><td>%.3f kg (%.2f%%)</td></tr>
            <tr><td><strong>Merma esperada</strong></td><td>%.3f kg</td></tr>
            <tr><td><strong>Coste merma</strong></td><td>%.2f €</td></tr>
            <tr><td><strong>Ahorro vs sector</strong></td><td>%.2f €</td></tr>
        </table>
        <p><a href="%s">Ver informe completo</a></p>',
            $report->getPeriodLabel(),
            $report->getStockStartKg(),
            $report->getInputKg(),
            $report->getConsumedKg(),
            $report->getStockEndKg(),
            $report->getActualWasteKg(),
            $report->getWastePct(),
            $report->getExpectedWasteKg(),
            $report->getWasteCostEuros(),
            $report->getSavedVsBaseline(),
            $this->appUrl
        );

        foreach ($recipientEmails as $recipient) {
            try {
                $this->mailer->send(
                    (new Email())
                        ->from($this->fromEmail)
                        ->to($recipient)
                        ->subject($subject)
                        ->html($htmlBody)
                );
                $this->logger->info('[MermaNotifier] Informe mensual enviado.', [
                    'recipient' => $recipient,
                    'reportId'  => $report->getId(),
                ]);
            } catch (TransportExceptionInterface $e) {
                $this->logger->error('[MermaNotifier] Error enviando informe mensual.', [
                    'recipient' => $recipient,
                    'error'     => $e->getMessage(),
                ]);
            }
        }
    }
}