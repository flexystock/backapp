<?php

namespace App\User\Infrastructure\OutputAdapters\Services;

use App\Entity\Main\User;
use App\Event\MailSentEvent;
use App\User\Application\OutputPorts\NotificationServiceInterface;
use Symfony\Component\Mailer\Exception\HttpTransportException;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class EmailNotificationService implements NotificationServiceInterface
{
    private MailerInterface $mailer;
    private UrlGeneratorInterface $urlGenerator;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(MailerInterface $mailer,
        UrlGeneratorInterface $urlGenerator,
        EventDispatcherInterface $eventDispatcher)
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailVerificationToUser(User $user): void
    {
        $verificationUrl = $this->urlGenerator->generate(
            'user_verification',
            ['token' => $user->getVerificationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $userName = $user->getName();
        $email = (new Email())
            ->from('flexystock@gmail.com')
            ->to($user->getEmail())
            ->subject('Verifica tu cuenta')
            ->html(
                '<p>Te damos la bienvenida desde FlexyStock.com. </p>'.
                '<p>Gracias por registrarte. Por favor,'.htmlspecialchars($userName).' haz clic en el siguiente enlace para verificar tu cuenta:</p>'.
                '<p><a href="'.$verificationUrl.'">Verificar Cuenta</a></p>'.
                '<p>Este enlace caducará a las 24 horas.</p>'
            );

        $this->mailer->send($email);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailToBack(User $user): void
    {
        $userName = $user->getName();
        $userEmail = $user->getEmail();
        $email = (new Email())
            ->from('flexystock@gmail.com')
            ->to('flexystock@gmail.com')
            ->subject('Nueva cuenta creada')
            ->html(
                '<p>Se acaba de registrar un nuevo cliente.</p>'.
                '<p>Nombre de Usuario: '.htmlspecialchars($userName).'.</p>'.
                '<p>Email de Usuario: '.htmlspecialchars($userEmail).'.</p>'
            );

        $this->mailer->send($email);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendPasswordResetEmail(User $user, $token): void
    {
        $userName = $user->getName();
        $email = (new Email())
            ->from('flexystock@gmail.com')
            ->to($user->getEmail())
            // ->to('khvhih756uyvdhobyg88@gmail.com')
            ->subject('Codigo restablecimiento de la contraseña')
            ->html(
                '<p>Generacioin de nueva password </p>'.
                '<p>Por favor,'.htmlspecialchars($userName).'</p>'.
                "<p>Su código de restablecimiento es: <strong>{$token}</strong></p>".
                '<p>Este código expirará en 15 minutos</p>');

        $status = 'success';
        $errorMessage = null;
        $errorCode = null;
        $errorType = null;

        try {
            $this->mailer->send($email);
        } catch (HttpTransportException $e) {
            $status = 'failure';
            $errorMessage = $e->getMessage();
            $errorCode = $e->getCode();
            $errorType = 'HttpTransportException';
        } catch (TransportException $e) {
            // Error relacionado con HTTP (por ejemplo, fallo al conectar con un servicio API)
            $status = 'failure';
            $errorMessage = $e->getMessage();
            $errorType = 'TransportException';
        } catch (TransportExceptionInterface$e) {
            // Otros errores de transporte
            $status = 'failure';
            $errorMessage = $e->getMessage();
            $errorType = 'TransportExceptionInterface';
        }

        // Despachar el evento
        $event = new MailSentEvent(
            $user->getEmail(),
            $email->getSubject(),
            $email->getHtmlBody(),
            $status,
            $errorMessage,
            $errorCode,
            new \DateTimeImmutable(),
            [
                'type' => 'password_reset',
                'token' => $token,
            ],
            $errorType,
            $user
        );

        $this->eventDispatcher->dispatch($event);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendSuccesfullPasswordResetEmail(User $user): void
    {
        $email = (new Email())
            ->from('flexystock@gmail.com')
            ->to($user->getEmail())
            ->subject('Nuevo password')
            ->html(
                '<p>Se ha restablecido su password correctamente </p>');

        $status = 'success';
        $errorMessage = null;
        $errorCode = null;
        $errorType = null;

        try {
            $this->mailer->send($email);
        } catch (HttpTransportException $e) {
            $status = 'failure';
            $errorMessage = $e->getMessage();
            $errorCode = $e->getCode();
            $errorType = 'HttpTransportException';
        } catch (TransportException $e) {
            // Error relacionado con HTTP (por ejemplo, fallo al conectar con un servicio API)
            $status = 'failure';
            $errorMessage = $e->getMessage();
            $errorType = 'TransportException';
        } catch (TransportExceptionInterface$e) {
            // Otros errores de transporte
            $status = 'failure';
            $errorMessage = $e->getMessage();
            $errorType = 'TransportExceptionInterface';
        }

        // Despachar el evento
        $event = new MailSentEvent(
            $user->getEmail(),
            $email->getSubject(),
            $email->getHtmlBody(),
            $status,
            $errorMessage,
            $errorCode,

            new \DateTimeImmutable(),
            [
                'type' => '',
                'token' => '',
            ],
            $errorType,
            $user
        );

        $this->eventDispatcher->dispatch($event);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailVerificationCreatedClientToUser(User $user): void
    {
        $verificationUrl = $this->urlGenerator->generate(
            'client_verification',
            ['token' => $user->getVerificationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $userName = $user->getName();
        $email = (new Email())
            ->from('flexystock@gmail.com')
            ->to($user->getEmail())
            ->subject('Verifica tu cliente')
            ->html(
                '<p>Gracias por registrar un nevo cliente </p>'.
                '<p>Gracias por registrarte. Por favor,'.htmlspecialchars($userName).' haz clic en el siguiente enlace para verificar el cliente:</p>'.
                '<p><a href="'.$verificationUrl.'">Verificar Cliente</a></p>'.
                '<p>Este enlace caducará a las 24 horas.</p>'
            );

        $this->mailer->send($email);
    }
}
