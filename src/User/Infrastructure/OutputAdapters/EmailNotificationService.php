<?php

namespace App\User\Infrastructure\OutputAdapters;

use App\Entity\Main\User;
use App\User\Application\OutputPorts\NotificationServiceInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class EmailNotificationService implements NotificationServiceInterface
{
    private MailerInterface $mailer;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $urlGenerator)
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
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
                '<p>Te damos la bienvenida desde FlexyStock.com. </p>' .
                '<p>Gracias por registrarte. Por favor,' . htmlspecialchars($userName) . ' haz clic en el siguiente enlace para verificar tu cuenta:</p>' .
                '<p><a href="' . $verificationUrl . '">Verificar Cuenta</a></p>'
            );

        $this->mailer->send($email);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailToBack(User $user):void
    {
        $userName = $user->getName();
        $userEmail = $user->getEmail();
        $email = (new Email())
            ->from('flexystock@gmail.com')
            ->to('flexystock@gmail.com')
            ->subject('Nueva cuenta creada')
            ->html(
                '<p>Se acaba de registrar un nuevo cliente.</p>' .
                '<p>Nombre de Usuario: ' . htmlspecialchars($userName) . '.</p>' .
                '<p>Email de Usuario: ' . htmlspecialchars($userEmail) . '.</p>'
            );

        $this->mailer->send($email);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendPasswordResetEmail(User $user, $token):void
    {
        $userName = $user->getName();
        $email = (new Email())
            ->from('flexystock@gmail.com')
            ->to($user->getEmail())
            ->subject('Codigo restablecimiento de la contraseña')
            ->html(
                '<p>Generacioin de nueva password </p>' .
                '<p>Por favor,' . htmlspecialchars($userName) . '</p>' .
                "<p>Su código de restablecimiento es: <strong>{$token}</strong></p>".
                '<p>Este código expirará en 15 minutos</p>');

        $this->mailer->send($email);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendSuccesfullPasswordResetEmail(User $user): void
    {
        $userName = $user->getName();
        $email = (new Email())
            ->from('flexystock@gmail.com')
            ->to($user->getEmail())
            ->subject('Nuevo password')
            ->html(
                '<p>Se ha restablecido su password correctamente </p>');

        $this->mailer->send($email);
    }
}