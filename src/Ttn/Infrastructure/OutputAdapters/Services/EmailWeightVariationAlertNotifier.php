<?php

namespace App\Ttn\Infrastructure\OutputAdapters\Services;

use App\Event\MailLogTarget;
use App\Event\MailSentEvent;
use App\Ttn\Application\DTO\WeightVariationAlertNotification;
use App\Ttn\Application\OutputPorts\WeightVariationAlertNotifierInterface;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EmailWeightVariationAlertNotifier implements WeightVariationAlertNotifierInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $logger,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly string $senderEmail
    ) {
    }

    public function notify(WeightVariationAlertNotification $notification): void
    {
        $recipientEmails = $notification->getRecipientEmails();
        $reasons = $this->buildReasons($notification);
        $subject = sprintf('Alerta de variación de peso: %s', $notification->getProductName());
        $textVariation = number_format($notification->getVariation(), 0, ',', '.');
        $textCurrent = number_format($notification->getCurrentWeight(), 0, ',', '.');
        $textPrevious = number_format($notification->getPreviousWeight(), 0, ',', '.');
        $textThreshold = number_format($notification->getWeightRange(), 0, ',', '.');
        $occurredAt = $notification->getOccurredAt()->format('d/m/Y H:i:s');
        $unit = $notification->getNameUnit();

        $textBody = sprintf(
            "Hola %s,\n\nSe ha detectado una variación de peso de %s %s en el producto '%s' (%s %s -> %s %s) el %s. " .
            "La variación supera el umbral configurado (%s %s) y se ha producido %s.\n\n" .
            "Te recomendamos revisar la báscula asociada al dispositivo %s.\n\nEquipo FlexyStock",
            $notification->getClientName(),
            $textVariation,
            $unit,
            $notification->getProductName(),
            $textPrevious,
            $unit,
            $textCurrent,
            $unit,
            $occurredAt,
            $textThreshold,
            $unit,
            $reasons,
            $notification->getDeviceId()
        );

        $htmlBody = sprintf(
            '<p>Hola %s,</p>' .
            '<p>Se ha detectado una variación de peso de <strong>%s %s</strong> en el producto <strong>%s</strong>' .
            ' (de %s %s a %s %s) el %s.</p>' .
            '<p>La variación supera el umbral configurado (<strong>%s %s</strong>) y se ha producido %s.</p>' .
            '<p>Te recomendamos revisar la báscula asociada al dispositivo <strong>%s</strong>.</p>' .
            '<p>Equipo FlexyStock</p>',
            htmlspecialchars($notification->getClientName(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            $textVariation,
            htmlspecialchars($unit, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            htmlspecialchars($notification->getProductName(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            $textPrevious,
            htmlspecialchars($unit, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            $textCurrent,
            htmlspecialchars($unit, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            $occurredAt,
            $textThreshold,
            htmlspecialchars($unit, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            htmlspecialchars($reasons, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            htmlspecialchars($notification->getDeviceId(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
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
            
            $this->logger->warning('[EmailWeightVariationAlertNotifier] No recipients configured for weight variation alert.', [
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

                $this->logger->error('[EmailWeightVariationAlertNotifier] Error enviando alerta de variación de peso.', [
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

    private function buildReasons(WeightVariationAlertNotification $notification): string
    {
        $reasons = [];

        if ($notification->isHoliday()) {
            $reasons[] = 'en un día festivo';
        }

        if ($notification->isOutsideBusinessHours()) {
            $reasons[] = 'fuera del horario comercial';
        }

        if (!$reasons) {
            $reasons[] = 'fuera de las condiciones configuradas';
        }

        if (2 === count($reasons)) {
            return implode(' y ', $reasons);
        }

        return $reasons[0];
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
        WeightVariationAlertNotification $notification
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
                'type' => 'weight_variation_alert',
                'uuidClient' => $notification->getUuidClient(),
                'productId' => $notification->getProductId(),
                'productName' => $notification->getProductName(),
                'scaleId' => $notification->getScaleId(),
                'deviceId' => $notification->getDeviceId(),
                'previousWeight' => $notification->getPreviousWeight(),
                'currentWeight' => $notification->getCurrentWeight(),
                'variation' => $notification->getVariation(),
                'weightRange' => $notification->getWeightRange(),
                'occurredAt' => $notification->getOccurredAt()->format(DateTimeImmutable::ATOM),
                'isHoliday' => $notification->isHoliday(),
                'outsideBusinessHours' => $notification->isOutsideBusinessHours(),
            ],
            errorType: $errorType,
            user: null,
            logTarget: MailLogTarget::CLIENT
        );

        $this->eventDispatcher->dispatch($event);
    }

    private function normalizeErrorCode(int|string|null $errorCode): ?int
    {
        if (null === $errorCode) {
            return null;
        }

        if (is_int($errorCode)) {
            return 0 !== $errorCode ? $errorCode : null;
        }

        return is_numeric($errorCode) ? ((int) $errorCode ?: null) : null;
    }
}
