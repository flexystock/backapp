<?php

declare(strict_types=1);

namespace App\ControlPanel\Purchase\Infrastructure\OutputAdapters\Services;

use App\ControlPanel\Purchase\Application\OutputPorts\EmailProcessPurchaseScalesServiceInterface;
use App\Entity\Main\Client;
use App\Entity\Main\PurchaseScales;
use App\Event\MailLogTarget;
use App\Event\MailSentEvent;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EmailProcessPurchaseScalesService implements EmailProcessPurchaseScalesServiceInterface
{
    private MailerInterface $mailer;
    private EventDispatcherInterface $eventDispatcher;
    private LoggerInterface $logger;

    public function __construct(
        MailerInterface $mailer,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    ) {
        $this->mailer = $mailer;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    public function sendScalesProcessingNotificationToClient(Client $client, PurchaseScales $purchaseScales): void
    {
        $this->logger->info('Attempting to send scales processing notification', [
            'uuidClient' => $client->getUuidClient(),
            'clientName' => $client->getName(),
            'uuidPurchase' => $purchaseScales->getUuidPurchase(),
        ]);

        $clientEmail = $this->getClientContactEmail($client);

        if (!$clientEmail) {
            $this->logger->warning('No email found for client', [
                'uuidClient' => $client->getUuidClient(),
            ]);
            return;
        }

        $email = (new Email())
            ->from('flexystock@gmail.com')
            ->to($clientEmail)
            ->subject('Sus balanzas están siendo creadas - FlexyStock')
            ->html($this->buildScalesProcessingNotificationHtml($client, $purchaseScales));

        [$status, $errorMessage, $errorCode, $errorType] = $this->sendCatching($email);

        $this->dispatchMailEvent(
            recipient: $clientEmail,
            subject: (string) $email->getSubject(),
            body: $email->getHtmlBody(),
            status: $status,
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            errorType: $errorType,
            type: 'scales_processing_notification',
            additional: [
                'uuidPurchase' => $purchaseScales->getUuidPurchase(),
                'uuidClient' => $purchaseScales->getUuidClient(),
                'quantity' => $purchaseScales->getQuantity(),
            ]
        );
    }

    private function buildScalesProcessingNotificationHtml(Client $client, PurchaseScales $purchaseScales): string
    {
        return sprintf(
            '<h2>Sus balanzas están siendo creadas</h2>'.
            '<p>Estimado/a cliente de <strong>%s</strong>,</p>'.
            '<p>Le informamos que su solicitud de <strong>%d balanza(s)</strong> está siendo procesada.</p>'.
            '<p>Nos pondremos en contacto con usted cuando las balanzas estén listas.</p>'.
            '<p><strong>Referencia de solicitud:</strong> %s</p>'.
            '<p>Gracias por confiar en FlexyStock.</p>'.
            '<p>Atentamente,<br>El equipo de FlexyStock</p>',
            htmlspecialchars($client->getName()),
            $purchaseScales->getQuantity(),
            htmlspecialchars($purchaseScales->getUuidPurchase())
        );
    }

    /**
     * @return array{string, ?string, ?int, ?string}
     */
    private function sendCatching(Email $email): array
    {
        try {
            $this->mailer->send($email);
            $this->logger->info('Email sent successfully', [
                'to' => $email->getTo()[0]->getAddress(),
                'subject' => $email->getSubject(),
            ]);
            return ['sent', null, null, null];
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Failed to send email', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);
            return ['failed', $e->getMessage(), $e->getCode(), get_class($e)];
        }
    }

    private function dispatchMailEvent(
        string $recipient,
        string $subject,
        ?string $body,
        string $status,
        ?string $errorMessage,
        ?int $errorCode,
        ?string $errorType,
        string $type,
        array $additional = []
    ): void {
        $event = new MailSentEvent(
            recipient: $recipient,
            subject: $subject,
            body: $body,
            status: $status,
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            sentAt: new DateTimeImmutable(),
            additionalData: array_merge(['type' => $type], $additional),
            errorType: $errorType,
            user: null,
            logTarget: MailLogTarget::MAIN
        );

        $this->eventDispatcher->dispatch($event);
    }

    private function getClientContactEmail(Client $client): ?string
    {
        $email = $client->getCompanyEmail();

        if (!$email) {
            $this->logger->warning('No company email found for client', [
                'uuidClient' => $client->getUuidClient(),
                'clientName' => $client->getName(),
            ]);
            return null;
        }

        $this->logger->info('Client contact email found', [
            'uuidClient' => $client->getUuidClient(),
            'email' => $email,
        ]);

        return $email;
    }
}