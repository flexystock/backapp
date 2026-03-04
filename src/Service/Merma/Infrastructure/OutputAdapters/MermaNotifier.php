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

    public function sendAnomalyAlert(ScaleEvent $event, array $recipientEmails = []): void
    {
        if (empty($recipientEmails)) {
            $this->logger->info('[MermaNotifier] sendAnomalyAlert: sin destinatarios, omitiendo.', [
                'scaleId'   => $event->getScaleId(),
                'productId' => $event->getProductId(),
            ]);
            return;
        }

        $subject = sprintf(
            '⚠️ Anomalía detectada — báscula #%d',
            $event->getScaleId()
        );

        $htmlBody = sprintf(
            '<h2>Anomalía de stock detectada</h2>
            <p>Se ha detectado una variación inesperada de peso fuera del horario de servicio.</p>
            <table cellpadding="6" style="border-collapse:collapse">
                <tr><td><strong>Báscula</strong></td><td>#%d</td></tr>
                <tr><td><strong>Producto</strong></td><td>#%d</td></tr>
                <tr><td><strong>Peso anterior</strong></td><td>%.3f kg</td></tr>
                <tr><td><strong>Peso actual</strong></td><td>%.3f kg</td></tr>
                <tr><td><strong>Variación</strong></td><td>%.3f kg</td></tr>
                <tr><td><strong>Detectado</strong></td><td>%s</td></tr>
            </table>
            <p><a href="%s">Revisar en FlexyStock</a></p>',
            $event->getScaleId(),
            $event->getProductId(),
            $event->getWeightBefore(),
            $event->getWeightAfter(),
            abs($event->getDeltaKg()),
            $event->getDetectedAt()->format('d/m/Y H:i:s'),
            $this->appUrl
        );

        $textBody = sprintf(
            "Anomalía detectada — báscula #%d\n\nVariación de %.3f kg fuera de horario.\nPeso anterior: %.3f kg\nPeso actual: %.3f kg\nDetectado: %s\n\nRevisar en: %s",
            $event->getScaleId(),
            abs($event->getDeltaKg()),
            $event->getWeightBefore(),
            $event->getWeightAfter(),
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
                    'scaleId'   => $event->getScaleId(),
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
            $report->getPeriodStart()->format('F Y')
        );

        $htmlBody = sprintf(
            '<h2>Informe mensual de merma</h2>
            <p>Periodo: <strong>%s — %s</strong></p>
            <table cellpadding="6" style="border-collapse:collapse">
                <tr><td><strong>Stock inicial</strong></td><td>%.3f kg</td></tr>
                <tr><td><strong>Entradas</strong></td><td>%.3f kg</td></tr>
                <tr><td><strong>Consumo</strong></td><td>%.3f kg</td></tr>
                <tr><td><strong>Stock final</strong></td><td>%.3f kg</td></tr>
                <tr><td><strong>Merma real</strong></td><td>%.3f kg (%.1f%%)</td></tr>
                <tr><td><strong>Merma esperada</strong></td><td>%.3f kg</td></tr>
            </table>
            <p><a href="%s">Ver informe completo</a></p>',
            $report->getPeriodStart()->format('d/m/Y'),
            $report->getPeriodEnd()->format('d/m/Y'),
            $report->getStockStart(),
            $report->getTotalInput(),
            $report->getTotalConsumed(),
            $report->getStockEnd(),
            $report->getActualWaste(),
            $report->getActualWastePct(),
            $report->getExpectedWaste(),
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