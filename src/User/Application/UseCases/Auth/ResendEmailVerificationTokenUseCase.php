<?php

namespace App\User\Application\UseCases\Auth;

use App\Entity\Main\User;
use App\User\Application\InputPorts\Auth\ResendEmailVerificationTokenInterface;   // <--- Asegúrate de usar este namespace
use App\User\Application\OutputPorts\NotificationServiceInterface;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;

class ResendEmailVerificationTokenUseCase implements ResendEmailVerificationTokenInterface
{
    private UserRepositoryInterface $userRepository;
    private NotificationServiceInterface $notificationService;

    public function __construct(UserRepositoryInterface $userRepository,
        NotificationServiceInterface $notificationService)
    {
        $this->userRepository = $userRepository;
        $this->notificationService = $notificationService;
    }

    public function resendEmailVerificationToken(User $user, string $token): bool
    {
        $user = $this->userRepository->findOneByVerificationToken($token);
        if (!$user) {
            return false;
        }
        if ($user->isVerified()) {
            return false;
        }
        // die("here");
        $user->setVerificationToken(null);
        $user->setVerificationTokenExpiresAt(null);
        $this->userRepository->save($user);

        $newVerificationToken = bin2hex(random_bytes(32));
        $user->setVerificationToken($newVerificationToken);
        $user->setVerificationTokenExpiresAt((new \DateTime())->modify('+1 day'));
        $this->userRepository->save($user);

        $this->notificationService->sendEmailToBack($user);

        return true;
    }
}
