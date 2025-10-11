<?php

namespace App\User\Infrastructure\OutputAdapters\Services;

use App\Entity\Main\User;
use App\Event\MailLogTarget;
use App\Event\MailSentEvent;
use App\User\Application\OutputPorts\NotificationServiceInterface;
use DateTimeImmutable;
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

    public function __construct(
        MailerInterface $mailer,
        UrlGeneratorInterface $urlGenerator,
        EventDispatcherInterface $eventDispatcher
    ) {
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

        $email = (new Email())
            ->from('flexystock@gmail.com')
            ->to($user->getEmail())
            ->subject('Verifica tu cuenta')
            ->html(
                '<p>Te damos la bienvenida desde FlexyStock.com.</p>'.
                '<p>Gracias por registrarte. Por favor, '.htmlspecialchars($user->getName()).' haz clic en el siguiente enlace para verificar tu cuenta:</p>'.
                '<p><a href="'.$verificationUrl.'">Verificar Cuenta</a></p>'.
                '<p>Este enlace caducará a las 24 horas.</p>'
            );

        [$status, $errorMessage, $errorCode, $errorType] = $this->sendCatching($email);

        $this->dispatchToMain(
            recipient: $user->getEmail(),
            subject: (string) $email->getSubject(),
            body: $email->getHtmlBody() ?? $email->getTextBody(),
            status: $status,
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            errorType: $errorType,
            type: 'user_verification',
            additional: [
                'userId' => $user->getUuid(),
                'userEmail' => $user->getEmail(),
                'verificationUrl' => $verificationUrl,
            ],
            user: $user
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailToBack(User $user): void
    {
        $verificationUrl = $this->urlGenerator->generate(
            'user_verification',
            ['token' => $user->getVerificationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $email = (new Email())
            ->from('flexystock@gmail.com')
            ->to('flexystock@gmail.com') // back/admin
            ->subject('Nueva cuenta creada')
            ->html(
                '<p>Se acaba de registrar un nuevo cliente.</p>'.
                '<p>Nombre de Usuario: '.htmlspecialchars($user->getName()).'.</p>'.
                '<p>Email de Usuario: '.htmlspecialchars($user->getEmail()).'.</p>'.
                '<p><a href="'.$verificationUrl.'">Verificar Cuenta</a></p>'.
                '<p>Este enlace caducará a las 24 horas.</p>'
            );

        [$status, $errorMessage, $errorCode, $errorType] = $this->sendCatching($email);

        // LOG EN MAIN: solicitud de registro recibida por el back
        $this->dispatchToMain(
            recipient: 'flexystock@gmail.com',
            subject: (string) $email->getSubject(),
            body: $email->getHtmlBody() ?? $email->getTextBody(),
            status: $status,
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            errorType: $errorType,
            type: 'registration_request',
            additional: [
                'userId' => $user->getUuid(),
                'userEmail' => $user->getEmail(),
                'verificationUrl' => $verificationUrl,
            ],
            user: $user
        );

        // Mantienes tu email informativo al usuario (si quieres que también quede logueado en MAIN, descomenta el dispatch dentro)
        $this->sendEmailAccountPendingVerificationToUser($user);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendPasswordResetEmail(User $user, $token): void
    {
        $email = (new Email())
            ->from('flexystock@gmail.com')
            ->to($user->getEmail())
            ->subject('Codigo restablecimiento de la contraseña')
            ->html(
                '<p>Generación de nueva password.</p>'.
                '<p>Por favor, '.htmlspecialchars($user->getName()).'</p>'.
                "<p>Su código de restablecimiento es: <strong>{$token}</strong></p>".
                '<p>Este código expirará en 15 minutos</p>'
            );

        [$status, $errorMessage, $errorCode, $errorType] = $this->sendCatching($email);

        $this->dispatchToMain(
            recipient: $user->getEmail(),
            subject: (string) $email->getSubject(),
            body: $email->getHtmlBody() ?? $email->getTextBody(),
            status: $status,
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            errorType: $errorType,
            type: 'password_reset',
            additional: ['token' => $token],
            user: $user
        );
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
            ->html('<p>Se ha restablecido su password correctamente.</p>');

        [$status, $errorMessage, $errorCode, $errorType] = $this->sendCatching($email);

        $this->dispatchToMain(
            recipient: $user->getEmail(),
            subject: (string) $email->getSubject(),
            body: $email->getHtmlBody() ?? $email->getTextBody(),
            status: $status,
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            errorType: $errorType,
            type: 'password_reset_success',
            additional: [],
            user: $user
        );
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

        $email = (new Email())
            ->from('flexystock@gmail.com')
            ->to($user->getEmail())
            ->subject('Verifica tu cliente')
            ->html(
                '<p>Gracias por registrar un nuevo cliente.</p>'.
                '<p>Por favor, '.htmlspecialchars($user->getName()).' haz clic en el siguiente enlace para verificar el cliente:</p>'.
                '<p><a href="'.$verificationUrl.'">Verificar Cliente</a></p>'.
                '<p>Este enlace caducará a las 24 horas.</p>'
            );

        [$status, $errorMessage, $errorCode, $errorType] = $this->sendCatching($email);

        $this->dispatchToMain(
            recipient: $user->getEmail(),
            subject: (string) $email->getSubject(),
            body: $email->getHtmlBody() ?? $email->getTextBody(),
            status: $status,
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            errorType: $errorType,
            type: 'client_verification',
            additional: [
                'userId' => $user->getUuid(),
                'verificationUrl' => $verificationUrl,
            ],
            user: $user
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailAccountPendingVerificationToUser(User $user): void
    {
        $email = (new Email())
            ->from('flexystock@gmail.com')
            ->to($user->getEmail())
            ->subject('Cuenta en proceso de verificación')
            ->html('<p>Su cuenta está en proceso de verificación y puede tardar unos minutos.</p>');

        [$status, $errorMessage, $errorCode, $errorType] = $this->sendCatching($email);

        $this->dispatchToMain(
            recipient: $user->getEmail(),
            subject: (string) $email->getSubject(),
            body: $email->getHtmlBody() ?? $email->getTextBody(),
            status: $status,
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            errorType: $errorType,
            type: 'account_pending_verification',
            additional: ['userId' => $user->getUuid()],
            user: $user
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailAccountVerifiedToUser(User $user): void
    {
        $email = (new Email())
            ->from('flexystock@gmail.com')
            ->to($user->getEmail())
            ->subject('Cuenta verificada')
            ->html('<p>Su cuenta ha sido verificada correctamente.</p>');

        [$status, $errorMessage, $errorCode, $errorType] = $this->sendCatching($email);

        // LOG EN MAIN: confirmación al usuario
        $this->dispatchToMain(
            recipient: $user->getEmail(),
            subject: (string) $email->getSubject(),
            body: $email->getHtmlBody() ?? $email->getTextBody(),
            status: $status,
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            errorType: $errorType,
            type: 'account_verified',
            additional: ['userId' => $user->getUuid()],
            user: $user
        );
    }

    public function sendNewUserInvitationEmail(User $user, string $forgotPasswordUrl): void
    {
        $email = (new Email())
            ->from('flexystock@gmail.com')
            ->to($user->getEmail())
            ->subject('Invitación a FlexyStock')
            ->html(
                '<p>Hola '.htmlspecialchars($user->getName()).',</p>'.
                '<p>Se ha creado una cuenta para ti en FlexyStock.</p>'.
                '<p>Para establecer tu contraseña inicial accede al siguiente enlace:</p>'.
                '<p><a href="'.$forgotPasswordUrl.'">Generar contraseña</a></p>'.
                '<p>Si el enlace no funciona, copia y pega la siguiente URL en tu navegador:</p>'.
                '<p>'.htmlspecialchars($forgotPasswordUrl).'</p>'
            );

        [$status, $errorMessage, $errorCode, $errorType] = $this->sendCatching($email);

        $this->dispatchToMain(
            recipient: $user->getEmail(),
            subject: (string) $email->getSubject(),
            body: $email->getHtmlBody() ?? $email->getTextBody(),
            status: $status,
            errorMessage: $errorMessage,
            errorCode: $errorCode,
            errorType: $errorType,
            type: 'user_invitation',
            additional: [
                'userId' => $user->getUuid(),
                'forgotPasswordUrl' => $forgotPasswordUrl,
            ],
            user: $user
        );
    }

    /**
     * Envía el mail y devuelve [status, errorMessage, errorCode, errorType].
     */
    private function sendCatching(Email $email): array
    {
        $status = 'success';
        $errorMessage = null;
        $errorCode = null;
        $errorType = null;

        try {
            $this->mailer->send($email);
        } catch (HttpTransportException $e) {
            $status = 'failure';
            $errorMessage = $e->getMessage();
            $errorCode = $this->normalizeErrorCode($e->getCode());
            $errorType = 'HttpTransportException';
        } catch (TransportException $e) {
            $status = 'failure';
            $errorMessage = $e->getMessage();
            $errorCode = $this->normalizeErrorCode($e->getCode());
            $errorType = 'TransportException';
        } catch (TransportExceptionInterface $e) {
            $status = 'failure';
            $errorMessage = $e->getMessage();
            $errorCode = $this->normalizeErrorCode($e->getCode());
            $errorType = 'TransportExceptionInterface';
        }

        return [$status, $errorMessage, $errorCode, $errorType];
    }

    private function dispatchToMain(
        string $recipient,
        string $subject,
        ?string $body,
        string $status,
        ?string $errorMessage,
        ?int $errorCode,
        ?string $errorType,
        string $type,
        array $additional = [],
        ?User $user = null
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
            user: $user,
            logTarget: MailLogTarget::MAIN
        );

        $this->eventDispatcher->dispatch($event);
    }

    private function normalizeErrorCode(int|string|null $code): ?int
    {
        if (null === $code) {
            return null;
        }
        if (is_int($code)) {
            return 0 !== $code ? $code : null;
        }

        return is_numeric($code) ? ((int) $code ?: null) : null;
    }
}
