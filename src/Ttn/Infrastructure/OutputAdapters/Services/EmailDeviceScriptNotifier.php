<?php

namespace App\Ttn\Infrastructure\OutputAdapters\Services;

use App\Event\MailLogTarget;
use App\Event\MailSentEvent;
use App\Ttn\Application\OutputPorts\DeviceScriptNotifierInterface;
use App\Ttn\Application\Services\DeviceScriptGenerator;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EmailDeviceScriptNotifier implements DeviceScriptNotifierInterface
{
    private MailerInterface $mailer;
    private DeviceScriptGenerator $scriptGenerator;
    private LoggerInterface $logger;
    private string $recipientEmail;
    private string $senderEmail;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        MailerInterface $mailer,
        DeviceScriptGenerator $scriptGenerator,
        LoggerInterface $logger,
        string $recipientEmail,
        string $senderEmail,
        EventDispatcherInterface $eventDispatcher,
    ) {
        $this->mailer = $mailer;
        $this->scriptGenerator = $scriptGenerator;
        $this->logger = $logger;
        $this->recipientEmail = $recipientEmail;
        $this->senderEmail = $senderEmail;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function notify(string $deviceId, string $devEui, string $joinEui, string $appKey): void
    {
        $script = $this->scriptGenerator->generate($devEui, $joinEui, $appKey);
        $subject = sprintf('Script Arduino para dispositivo %s', $deviceId);

        $htmlBody = sprintf(
            '<p>Se ha registrado el dispositivo <strong>%s</strong> en TTN.</p>
         <p>A continuación se incluye el script de Arduino que debes cargar en el equipo:</p>
         <pre>%s</pre>',
            htmlspecialchars($deviceId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            htmlspecialchars($script, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
        );

        $email = (new Email())
            ->from($this->senderEmail)
            ->to($this->recipientEmail)
            ->subject($subject)
            ->text($script)
            ->html($htmlBody);

        $status = 'success';
        $errorMessage = null;
        $errorCode = null;
        $errorType = null;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            $status = 'failure';
            $errorMessage = $exception->getMessage();
            $errorCode = $this->normalizeErrorCode($exception->getCode());
            $errorType = get_debug_type($exception);

            $this->logger->error('No se pudo enviar el email con el script del dispositivo TTN.', [
                'deviceId' => $deviceId,
                'exception' => $exception->getMessage(),
            ]);

            // registra el intento fallido en MAIN
            $this->dispatchMailSentEvent($email, $status, $errorMessage, $deviceId, $devEui, $joinEui, $errorCode, $errorType);

            throw new \RuntimeException('Error enviando el script del dispositivo por email', 0, $exception);
        }

        // registra el éxito en MAIN
        $this->dispatchMailSentEvent($email, $status, $errorMessage, $deviceId, $devEui, $joinEui, $errorCode, $errorType);
    }

    private function dispatchMailSentEvent(
        Email $email,
        string $status,
        ?string $errorMessage,
        string $deviceId,
        string $devEui,
        string $joinEui,
        ?int $errorCode = null,
        ?string $errorType = null,
    ): void {
        $event = new MailSentEvent(
            recipient: $this->recipientEmail,
            subject: $email->getSubject() ?? '',
            body: $email->getHtmlBody() ?? $email->getTextBody(),
            status: $status,
            errorMessage: $errorMessage,
            errorCode: $errorCode,              // <- ahora sí en su sitio
            sentAt: new DateTimeImmutable(), // <- y también en su sitio
            additionalData: [
                'type' => 'ttn_device_script',
                'deviceId' => $deviceId,
                'devEui' => $devEui,
                'joinEui' => $joinEui,
            ],
            errorType: $errorType,
            user: null,
            logTarget: MailLogTarget::MAIN      // <- SOLO main
        );

        $this->eventDispatcher->dispatch($event);
    }

    private function normalizeErrorCode(int|string $errorCode): ?int
    {
        if (is_int($errorCode)) {
            return 0 !== $errorCode ? $errorCode : null;
        }

        return is_numeric($errorCode) ? (int) $errorCode : null;
    }
}
