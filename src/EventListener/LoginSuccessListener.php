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

        // Solo ejecutar en login real, no en cada request JWT autenticado
        $request = $event->getRequest();
        $path = $request->getPathInfo();
        if (!str_contains($path, '/api/login') && !str_contains($path, '/login')) {
            return;
        }

        try {
            $ipAddress = $request->getClientIp() ?? 'unknown';
            $user->setLastAccess(new \DateTimeImmutable());
            $user->setFailedAttempts(0);
            $user->setLockedUntil(null);

            $loginRecord = new Login();
            $loginRecord->setUuidUser($user->getUuid());
            $loginRecord->setLoginAt(new \DateTimeImmutable());
            $loginRecord->setIpAddress($ipAddress);

            $this->entityManager->persist($user);
            $this->entityManager->persist($loginRecord);
            $this->entityManager->flush();
        } catch (\Throwable $e) {
            // No reventar la request por un fallo de auditoría
        }
    }
}