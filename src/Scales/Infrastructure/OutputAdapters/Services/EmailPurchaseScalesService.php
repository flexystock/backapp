<?php

declare(strict_types=1);

namespace App\Scales\Infrastructure\OutputAdapters\Services;

use App\Entity\Main\Client;
use App\Entity\Main\PurchaseScales;
use App\Event\MailLogTarget;
use App\Event\MailSentEvent;
use App\Scales\Application\OutputPorts\EmailPurchaseScalesServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EmailPurchaseScalesService implements EmailPurchaseScalesServiceInterface
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

    public function sendPurchaseNotificationToFlexystock(PurchaseScales $purchaseScales): void
    {
        $email = (new Email())
            ->from('flexystock@gmail.com')
            ->to('flexystock@gmail.com')
            ->subject('Nueva solicitud de balanzas - '.$purchaseScales->getClientName())
            ->html($this->buildPurchaseNotificationHtml($purchaseScales));

        [$status, $errorMessage, $errorCode, $errorType] = $this->sendCatching($email);

        $this->dispatchMailEvent(
            recipient: 'flexystock@gmail.com',
            subject: (string) $email->getSubject(),
            body: $email->getHtmlBody(),
            status: $status,
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            errorType: $errorType,
            type: 'purchase_scales_notification',
            additional: [
                'uuidPurchase' => $purchaseScales->getUuidPurchase(),
                'uuidClient' => $purchaseScales->getUuidClient(),
                'quantity' => $purchaseScales->getQuantity(),
            ]
        );
    }

    public function sendScalesProcessingNotificationToClient(Client $client, PurchaseScales $purchaseScales): void
    {
        // Get client's primary contact email (assuming there's a way to get it)
        // For now, we'll need to determine how to get client contact email
        // This might need to be passed as a parameter or retrieved from client entity
        
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

    private function buildPurchaseNotificationHtml(PurchaseScales $purchaseScales): string
    {
        return sprintf(
            '<h2>Nueva Solicitud de Balanzas</h2>'.
            '<p><strong>Cliente:</strong> %s</p>'.
            '<p><strong>UUID Cliente:</strong> %s</p>'.
            '<p><strong>Cantidad solicitada:</strong> %d</p>'.
            '<p><strong>Fecha de solicitud:</strong> %s</p>'.
            '<p><strong>UUID de compra:</strong> %s</p>'.
            '<p><strong>Estado:</strong> %s</p>'.
            '<p>Por favor, procese esta solicitud desde el panel de control.</p>',
            htmlspecialchars($purchaseScales->getClientName()),
            htmlspecialchars($purchaseScales->getUuidClient()),
            $purchaseScales->getQuantity(),
            $purchaseScales->getPurchaseAt()->format('d/m/Y H:i:s'),
            htmlspecialchars($purchaseScales->getUuidPurchase()),
            htmlspecialchars($purchaseScales->getStatus())
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
     * Send email and catch exceptions.
     *
     * @return array{string, ?string, ?int, ?string} [status, errorMessage, errorCode, errorType]
     */
    private function sendCatching(Email $email): array
    {
        try {
            $this->mailer->send($email);
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
            target: MailLogTarget::MAIN,
            recipient: $recipient,
            subject: $subject,
            body: $body,
            status: $status,
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            errorType: $errorType,
            type: $type,
            additional: $additional
        );

        $this->eventDispatcher->dispatch($event, MailSentEvent::NAME);
    }

    /**
     * Get client contact email.
     * 
     * TODO: Implement logic to get client contact email.
     * This needs to query the appropriate user or contact table to find
     * the primary contact email for the client. Options include:
     * 1. Query users table for users associated with this client
     * 2. Add a contact_email field to the client table
     * 3. Create a separate client_contacts table
     * 
     * For now, returning null and logging a warning.
     * The sendScalesProcessingNotificationToClient method will not send
     * emails until this is implemented.
     *
     * @param Client $client The client to get contact email for
     * @return string|null The client's contact email, or null if not found
     */
    private function getClientContactEmail(Client $client): ?string
    {
        $this->logger->warning('getClientContactEmail not implemented', [
            'uuidClient' => $client->getUuidClient(),
            'clientName' => $client->getName(),
        ]);
        
        // TODO: Implement one of these approaches:
        // Option 1: Query users associated with client
        // $users = $this->entityManager->getRepository(User::class)
        //     ->findBy(['clients' => $client], [], 1);
        // return $users ? $users[0]->getEmail() : null;
        
        // Option 2: If client has a contact_email field
        // return $client->getContactEmail();
        
        // Option 3: Query a client_contacts table
        // $contact = $this->entityManager->getRepository(ClientContact::class)
        //     ->findOneBy(['client' => $client, 'is_primary' => true]);
        // return $contact ? $contact->getEmail() : null;
        
        return null;
    }
}
