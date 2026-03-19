<?php

namespace App\EventListener;

use App\Entity\Main\Login;
use App\Entity\Main\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

#[AsEventListener(event: LoginSuccessEvent::class)]
class LoginSuccessListener
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    public function __invoke(LoginSuccessEvent $event): void
    {
        $user = $event->getAuthenticatedToken()->getUser();

        if (!$user instanceof User) {
            return;
        }

        if (!$user->isActive()) {
            return;
        }

        $ipAddress = $event->getRequest()->getClientIp() ?? 'unknown';

        $user->setLastAccess(new \DateTimeImmutable());          // ← añade esto
        $user->setFailedAttempts(0);         // ← y esto, para mantener la lógica anterior
        $user->setLockedUntil(null);

        $loginRecord = new Login();
        $loginRecord->setUuidUser($user->getUuid());
        $loginRecord->setLoginAt(new \DateTimeImmutable());
        $loginRecord->setIpAddress($ipAddress);

        $this->entityManager->persist($user);
        $this->entityManager->persist($loginRecord);
        $this->entityManager->flush();
    }
}