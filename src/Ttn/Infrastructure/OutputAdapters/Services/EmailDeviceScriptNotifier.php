<?php

namespace App\Ttn\Infrastructure\OutputAdapters\Services;

use App\Ttn\Application\OutputPorts\DeviceScriptNotifierInterface;
use App\Ttn\Application\Services\DeviceScriptGenerator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailDeviceScriptNotifier implements DeviceScriptNotifierInterface
{
    private MailerInterface $mailer;
    private DeviceScriptGenerator $scriptGenerator;
    private LoggerInterface $logger;
    private string $recipientEmail;
    private string $senderEmail;

    public function __construct(
        MailerInterface $mailer,
        DeviceScriptGenerator $scriptGenerator,
        LoggerInterface $logger,
        string $recipientEmail,
        string $senderEmail
    ) {
        $this->mailer = $mailer;
        $this->scriptGenerator = $scriptGenerator;
        $this->logger = $logger;
        $this->recipientEmail = $recipientEmail;
        $this->senderEmail = $senderEmail;
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

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $exception) {
            $this->logger->error('No se pudo enviar el email con el script del dispositivo TTN.', [
                'deviceId' => $deviceId,
                'exception' => $exception->getMessage(),
            ]);

            throw new \RuntimeException('Error enviando el script del dispositivo por email', 0, $exception);
        }
    }
}
