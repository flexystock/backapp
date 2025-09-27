<?php

namespace App\Ttn\Infrastructure\OutputAdapters\Services;

use App\Entity\Client\LogMail as ClientLogMail;
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
        $recipientEmail = $notification->getRecipientEmail();
        $subject = sprintf('Alerta de stock bajo: %s', $notification->getProductName());
        $textBody = sprintf(
            "Hola %s,\n\nLa báscula asociada al producto '%s' ha registrado un peso actual de %.2f, que se encuentra por debajo del stock mínimo configurado (%.2f).\n\nPor favor, revisa tu inventario para reponer existencias.\n\nEquipo FlexyStock",
            $notification->getClientName(),
            $notification->getProductName(),
            $notification->getCurrentWeight(),
            $notification->getMinimumStock()
        );
        $htmlBody = sprintf(
            '<p>Hola %s,</p>' .
            "<p>La báscula asociada al producto <strong>%s</strong> ha registrado un peso actual de <strong>%.2f</strong>, que se encuentra por debajo del stock mínimo configurado (<strong>%.2f</strong>).</p>" .
            '<p>Por favor, revisa tu inventario para reponer existencias.</p>' .
            '<p>Equipo FlexyStock</p>',
            htmlspecialchars($notification->getClientName(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            htmlspecialchars($notification->getProductName(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            $notification->getCurrentWeight(),
            $notification->getMinimumStock()
        );

        $status = 'success';
        $errorMessage = null;
        $errorCode = null;
        $errorType = null;
        $sentAt = new DateTimeImmutable();
        $recipientForLog = $recipientEmail ?? 'not-configured';

        if (!$recipientEmail) {
            $status = 'failure';
            $errorMessage = 'El cliente no tiene un correo electrónico configurado.';
            $errorType = 'missing_recipient';
        } else {
            $email = (new Email())
                ->from($this->senderEmail)
                ->to($recipientEmail)
                ->subject($subject)
                ->text($textBody)
                ->html($htmlBody);

            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $exception) {
                $status = 'failure';
                $errorMessage = $exception->getMessage();
                $errorCode = $this->normalizeErrorCode($exception->getCode());
                $errorType = get_debug_type($exception);

                $this->logger->error('[EmailMinimumStockNotifier] Error enviando alerta de stock.', [
                    'exception' => $exception->getMessage(),
                    'uuidClient' => $notification->getUuidClient(),
                    'productId' => $notification->getProductId(),
                ]);
            }
        }

        $this->dispatchMailSentEvent(
            $recipientForLog,
            $subject,
            $htmlBody,
            $status,
            $errorMessage,
            $errorCode,
            $sentAt,
            $errorType,
            $notification
        );

        $this->logInClientDatabase(
            $notification,
            $recipientForLog,
            $subject,
            $htmlBody,
            $status,
            $errorMessage,
            $errorCode,
            $errorType,
            $sentAt
        );
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
            $recipient,
            $subject,
            $body,
            $status,
            $errorMessage,
            $errorCode,
            $sentAt,
            [
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
            $errorType
        );

        $this->eventDispatcher->dispatch($event);
    }

    private function logInClientDatabase(
        MinimumStockNotification $notification,
        string $recipient,
        string $subject,
        string $body,
        string $status,
        ?string $errorMessage,
        ?int $errorCode,
        ?string $errorType,
        DateTimeImmutable $sentAt
    ): void {
        try {
            $entityManager = $this->connectionManager->getEntityManager($notification->getUuidClient());

            $logMail = new ClientLogMail();
            $logMail->setRecipient($recipient);
            $logMail->setSubject($subject);
            $logMail->setBody($body);
            $logMail->setStatus($status);
            $logMail->setErrorMessage($errorMessage);
            $logMail->setErrorCode($errorCode);
            $logMail->setErrorType($errorType);
            $logMail->setSentAt($sentAt);
            $logMail->setAdditionalData([
                'type' => 'stock_alert',
                'uuidClient' => $notification->getUuidClient(),
                'productId' => $notification->getProductId(),
                'productName' => $notification->getProductName(),
                'scaleId' => $notification->getScaleId(),
                'deviceId' => $notification->getDeviceId(),
                'currentWeight' => $notification->getCurrentWeight(),
                'minimumStock' => $notification->getMinimumStock(),
                'weightRange' => $notification->getWeightRange(),
            ]);

            $entityManager->persist($logMail);
            $entityManager->flush();

            $this->logger->info('[EmailMinimumStockNotifier] Registro de notificación almacenado en la base de datos del cliente.', [
                'uuidClient' => $notification->getUuidClient(),
                'status' => $status,
            ]);
        } catch (\Throwable $exception) {
            $this->logger->error('[EmailMinimumStockNotifier] Error guardando log_mail en la base de datos del cliente.', [
                'uuidClient' => $notification->getUuidClient(),
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    private function normalizeErrorCode(int|string $errorCode): ?int
    {
        if (is_int($errorCode)) {
            return 0 !== $errorCode ? $errorCode : null;
        }

        return is_numeric($errorCode) ? ((int) $errorCode ?: null) : null;
    }
}
