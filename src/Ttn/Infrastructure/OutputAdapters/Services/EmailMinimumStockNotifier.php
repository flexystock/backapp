<?php

namespace App\Ttn\Infrastructure\OutputAdapters\Services;

use App\Event\MailLogTarget;
use App\Event\MailSentEvent;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Ttn\Application\DTO\MinimumStockNotification;
use App\Ttn\Application\OutputPorts\MinimumStockNotificationInterface;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EmailMinimumStockNotifier implements MinimumStockNotificationInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $logger,
        private readonly ClientConnectionManager $connectionManager,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly string $senderEmail
    ) {
    }

    public function notify(MinimumStockNotification $notification): void
    {
        $recipientEmails = $notification->getRecipientEmails();
        $subject = sprintf('Alerta de stock bajo: %s', $notification->getProductName());
        
        // Convert current weight from kg to the configured unit
        // Formula: weight_in_units = weight_kg / (kg_per_unit)
        // Example: 0.5797 kg / 0.01054 kg/unit = 55 units
        $conversionFactor = $notification->getConversionFactor() ?? 1.0;
        $currentWeightInUnits = $conversionFactor > 0 
            ? $notification->getCurrentWeight() / $conversionFactor 
            : $notification->getCurrentWeight();
        
        $txtActual = number_format($currentWeightInUnits, 0, '.', '');
        $txtMin = number_format($notification->getMinimumStock(), 0, '.', '');

        $textBody = sprintf(
            "Hola %s,\n\nLa báscula asociada al producto '%s' ha registrado un stock actual de %s %s, que se encuentra por debajo del stock mínimo configurado (%s %s).\n\nPor favor, revisa tu inventario para reponer existencias.\n\nEquipo FlexyStock",
            $notification->getClientName(),
            $notification->getProductName(),
            $txtActual,
            $notification->getNameUnit(),
            $txtMin,
            $notification->getNameUnit()
        );

        $htmlBody = sprintf(
            '<p>Hola %s,</p>'.
            '<p>La báscula asociada al producto <strong>%s</strong> ha registrado un peso actual de <strong>%s %s</strong>, que se encuentra por debajo del stock mínimo configurado (<strong>%s %s</strong>).</p>'.
            '<p>Por favor, revisa tu inventario para reponer existencias.</p>'.
            '<p>Equipo FlexyStock</p>',
            htmlspecialchars($notification->getClientName(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            htmlspecialchars($notification->getProductName(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            $txtActual,
            htmlspecialchars($notification->getNameUnit(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            $txtMin,
            htmlspecialchars($notification->getNameUnit(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
        );

        $status = 'success';
        $errorMessage = null;
        $errorCode = null;
        $errorType = null;
        $sentAt = new DateTimeImmutable();

        if (empty($recipientEmails)) {
            $status = 'failure';
            $errorMessage = 'No hay destinatarios configurados para este tipo de alarma.';
            $errorType = 'missing_recipients';
            
            $this->dispatchMailSentEvent(
                'not-configured',
                $subject,
                $htmlBody,
                $status,
                $errorMessage,
                $errorCode,
                $sentAt,
                $errorType,
                $notification
            );
            
            $this->logger->warning('[EmailMinimumStockNotifier] No recipients configured for stock alert.', [
                'uuidClient' => $notification->getUuidClient(),
                'productId' => $notification->getProductId(),
            ]);
            
            return;
        }

        // Send email to all recipients
        foreach ($recipientEmails as $recipientEmail) {
            $emailStatus = 'success';
            $emailErrorMessage = null;
            $emailErrorCode = null;
            $emailErrorType = null;

            $email = (new Email())
                ->from($this->senderEmail)
                ->to($recipientEmail)
                ->subject($subject)
                ->text($textBody)
                ->html($htmlBody);

            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $exception) {
                $emailStatus = 'failure';
                $emailErrorMessage = $exception->getMessage();
                $emailErrorCode = $this->normalizeErrorCode($exception->getCode());
                $emailErrorType = get_debug_type($exception);

                $this->logger->error('[EmailMinimumStockNotifier] Error enviando alerta de stock.', [
                    'exception' => $exception->getMessage(),
                    'uuidClient' => $notification->getUuidClient(),
                    'productId' => $notification->getProductId(),
                    'recipient' => $recipientEmail,
                ]);
            }

            $this->dispatchMailSentEvent(
                $recipientEmail,
                $subject,
                $htmlBody,
                $emailStatus,
                $emailErrorMessage,
                $emailErrorCode,
                $sentAt,
                $emailErrorType,
                $notification
            );
        }
    }

    private function dispatchMailSentEvent(
        string $recipient,
        string $subject,
        string $body,
        string $status,
        ?string $errorMessage,
        ?int $errorCode,
        DateTimeImmutable $sentAt,
        ?string $errorType,
        MinimumStockNotification $notification
    ): void {
        $event = new MailSentEvent(
            recipient: $recipient,
            subject: $subject,
            body: $body,
            status: $status,
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            sentAt: $sentAt,
            additionalData: [
                'type' => 'stock_alert',
                'uuidClient' => $notification->getUuidClient(),
                'productId' => $notification->getProductId(),
                'productName' => $notification->getProductName(),
                'scaleId' => $notification->getScaleId(),
                'deviceId' => $notification->getDeviceId(),
                'currentWeight' => $notification->getCurrentWeight(),
                'minimumStock' => $notification->getMinimumStock(),
                'weightRange' => $notification->getWeightRange(),
            ],
            errorType: $errorType,
            user: null,
            logTarget: MailLogTarget::CLIENT
        );

        $this->eventDispatcher->dispatch($event);
    }

    private function normalizeErrorCode(int|string $errorCode): ?int
    {
        if (is_int($errorCode)) {
            return 0 !== $errorCode ? $errorCode : null;
        }

        return is_numeric($errorCode) ? ((int) $errorCode ?: null) : null;
    }
}
