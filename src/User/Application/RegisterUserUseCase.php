<?php
namespace App\User\Application;

use App\Entity\Main\User;
use App\User\Infrastructure\InputPorts\RegisterUserInputPort;
use App\User\Infrastructure\OutputPorts\UserRepositoryInterface;
use Cassandra\Exception\ValidationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\User\Application\DTO\CreateUserRequest;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\User\Infrastructure\OutputPorts\NotificationServiceInterface;
class RegisterUserUseCase implements RegisterUserInputPort
{
    private UserRepositoryInterface $userRepositoryInterface;
    private UserPasswordHasherInterface $passwordHasher;
    private ValidatorInterface $validator;
    private MailerInterface $mailer;
    private UrlGeneratorInterface $urlGenerator;
    private NotificationServiceInterface $notificationService;
    private $entityManager;

    public function __construct(UserRepositoryInterface $userRepositoryInterface,
                                UserPasswordHasherInterface $passwordHasher,
                                ValidatorInterface $validator,
                                MailerInterface $mailer,
                                UrlGeneratorInterface $urlGenerator,
                                NotificationServiceInterface $notificationService)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
        $this->passwordHasher = $passwordHasher;
        $this->validator = $validator;
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->notificationService = $notificationService;
    }

    /**
     * @throws \DateMalformedStringException
     * @throws RandomException
     * @throws \Exception
     */
    public function register(CreateUserRequest $data): User
    {
        $user = User::fromCreateUserRequest($data, $this->passwordHasher);
        $this->userRepositoryInterface->save($user);
        return $user;
    }

    /**
     * @throws \DateMalformedStringException
     * @throws RandomException
     */
    private function generateVerificationToken(User $user): void
    {
        $verificationToken = bin2hex(random_bytes(32));
        $user->setVerificationToken($verificationToken);
        $user->setVerificationTokenExpiresAt((new \DateTime())->modify('+1 day'));
    }

    private function validateUser(User $user): void
    {
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            throw new ValidationException((string) $errors);
        }
    }

    /**
     * @throws \Exception
     */
    private function saveUserAndSendNotifications(User $user): void
    {
        try {
            $this->userRepositoryInterface->save($user);

            //$this->notificationService->sendEmailVerificationToUser($user);
            //$this->notificationService->sendEmailToBack($user);

            //$this->entityManager->commit();
        } catch (\Exception $e) {
            throw $e;
        }
    }



}