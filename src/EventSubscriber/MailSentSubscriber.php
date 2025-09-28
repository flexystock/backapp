<?php

// src/EventSubscriber/MailSentSubscriber.php

namespace App\EventSubscriber;

use App\Entity\Client\LogMail as ClientLogMail;
use App\Entity\Main\LogMail as MainLogMail;
use App\Event\MailLogTarget;
use App\Event\MailSentEvent;
use App\Infrastructure\Services\ClientConnectionManager;
use Doctrine\ORM\EntityManagerInterface;     // entidad de la BBDD main
use Psr\Log\LoggerInterface; // entidad de la BBDD cliente
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MailSentSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $emMain,                // EM por defecto (main)
        private ClientConnectionManager $connectionManager,    // para obtener EM del cliente
        private LoggerInterface $logger,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [MailSentEvent::class => 'onMailSent'];
    }

    public function onMailSent(MailSentEvent $event): void
    {
        $target = $event->getLogTarget();
        $data = $event->getAdditionalData() ?? [];
        $uuid = $data['uuidClient'] ?? null;

        // MAIN
        if (MailLogTarget::MAIN === $target || MailLogTarget::BOTH === $target) {
            try {
                $this->persistInMain($event);
            } catch (\Throwable $e) {
                $this->logger->error('[MailSentSubscriber] Error guardando en MAIN', [
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        // CLIENT
        if ((MailLogTarget::CLIENT === $target || MailLogTarget::BOTH === $target) && $uuid) {
            try {
                $this->persistInClient($event, $uuid);
            } catch (\Throwable $e) {
                $this->logger->error('[MailSentSubscriber] Error guardando en CLIENT', [
                    'uuidClient' => $uuid,
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        // Si piden CLIENT pero no hay uuid, lo anotamos
        if ((MailLogTarget::CLIENT === $target || MailLogTarget::BOTH === $target) && !$uuid) {
            $this->logger->warning('[MailSentSubscriber] logTarget CLIENT/BOTH pero falta uuidClient; no se guarda en cliente.');
        }
    }

    private function persistInMain(MailSentEvent $event): void
    {
        $log = new MainLogMail();
        $log->setRecipient($event->getRecipient());
        $log->setSubject($event->getSubject());
        $log->setBody($event->getBody());
        $log->setStatus($event->getStatus());
        $log->setErrorMessage($event->getErrorMessage());
        $log->setErrorCode($event->getErrorCode());
        $log->setErrorType($event->getErrorType());
        $log->setSentAt(\DateTimeImmutable::createFromInterface($event->getSentAt()));
        $log->setAdditionalData($event->getAdditionalData());

        $this->emMain->persist($log);
        $this->emMain->flush();
    }

    private function persistInClient(MailSentEvent $event, string $uuidClient): void
    {
        $em = $this->connectionManager->getEntityManager($uuidClient);

        $log = new ClientLogMail();
        $log->setRecipient($event->getRecipient());
        $log->setSubject($event->getSubject());
        $log->setBody($event->getBody());
        $log->setStatus($event->getStatus());
        $log->setErrorMessage($event->getErrorMessage());
        $log->setErrorCode($event->getErrorCode());
        $log->setErrorType($event->getErrorType());
        $log->setSentAt(\DateTimeImmutable::createFromInterface($event->getSentAt()));
        $log->setAdditionalData($event->getAdditionalData());

        $em->persist($log);
        $em->flush();
    }
}
