<?php
// src/EventSubscriber/MailSentSubscriber.php

namespace App\EventSubscriber;

use App\Entity\Main\LogMail;
use App\Event\MailSentEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Persistence\ManagerRegistry;


class MailSentSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $entityManagerMain;

    public function __construct(ManagerRegistry $registry)
    {
        // Obtiene el EntityManager específico para 'main'
        $this->entityManagerMain = $registry->getManager('main');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MailSentEvent::class => 'onMailSent',
        ];
    }

    public function onMailSent(MailSentEvent $event): void
    {
        $logMail = new LogMail();
        $logMail->setRecipient($event->getRecipient());
        $logMail->setSubject($event->getSubject());
        $logMail->setBody($event->getBody());
        $logMail->setStatus($event->getStatus());
        $logMail->setErrorMessage($event->getErrorMessage());
        $logMail->setSentAt($event->getSentAt());
        $logMail->setAdditionalData($event->getAdditionalData());
        $logMail->setErrorCode($event->getErrorCode());
        $logMail->setErrorType($event->getErrorType());

        $this->entityManagerMain->persist($logMail);
        $this->entityManagerMain->flush();
    }
}
