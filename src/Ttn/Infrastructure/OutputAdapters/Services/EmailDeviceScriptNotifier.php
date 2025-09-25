<?php

namespace App\Ttn\Infrastructure\OutputAdapters\Services;

use App\Event\MailSentEvent;
use App\Ttn\Application\OutputPorts\DeviceScriptNotifierInterface;
use App\Ttn\Application\Services\DeviceScriptGenerator;
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
        EventDispatcherInterface $eventDispatcher
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
            '<p>Se ha registrado el dispositivo <strong>%s</strong> en TTN.</p>' .
            '<p>A continuación se incluye el script de Arduino que debes cargar en el equipo:</p>' .
            '<pre>%s</pre>',
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

            $this->dispatchMailSentEvent($email, $status, $errorMessage, $errorCode, $errorType, $deviceId, $devEui, $joinEui);

            throw new \RuntimeException('Error enviando el script del dispositivo por email', 0, $exception);
        }

        $this->dispatchMailSentEvent($email, $status, $errorMessage, $errorCode, $errorType, $deviceId, $devEui, $joinEui);
    }

    private function dispatchMailSentEvent(
        Email $email,
        string $status,
        ?string $errorMessage,
        ?int $errorCode,
        ?string $errorType,
        string $deviceId,
        string $devEui,
        string $joinEui
    ): void {
        $event = new MailSentEvent(
            $this->recipientEmail,
            $email->getSubject() ?? '',
            $email->getHtmlBody() ?? $email->getTextBody(),
            $status,
            $errorMessage,
            $errorCode,
            new \DateTimeImmutable(),
            [
                'type' => 'ttn_device_script',
                'deviceId' => $deviceId,
                'devEui' => $devEui,
                'joinEui' => $joinEui,
            ],
            $errorType
        );

        $this->eventDispatcher->dispatch($event);
    }

    private function normalizeErrorCode(int|string $errorCode): ?int
    {
        if (is_int($errorCode)) {
            return $errorCode !== 0 ? $errorCode : null;
        }

        return is_numeric($errorCode) ? (int) $errorCode : null;
    }
}