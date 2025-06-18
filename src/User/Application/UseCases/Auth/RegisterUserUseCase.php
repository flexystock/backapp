<?php

namespace App\User\Application\UseCases\Auth;

use App\Entity\Main\User;
use App\User\Application\DTO\Auth\CreateUserRequest;
use App\User\Application\InputPorts\Auth\RegisterUserInputPort;
use App\User\Application\OutputPorts\NotificationServiceInterface;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use App\Admin\Role\Infrastructure\OutputAdapters\Repositories\RoleRepository;
use App\User\Application\RandomException;
use Cassandra\Exception\ValidationException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegisterUserUseCase implements RegisterUserInputPort
{
    private UserRepositoryInterface $userRepositoryInterface;
    private UserPasswordHasherInterface $passwordHasher;
    private ValidatorInterface $validator;
    private MailerInterface $mailer;
    private UrlGeneratorInterface $urlGenerator;
    private NotificationServiceInterface $notificationService;
    private RoleRepository $roleRepository;


    public function __construct(UserRepositoryInterface $userRepositoryInterface,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        MailerInterface $mailer,
        UrlGeneratorInterface $urlGenerator,
        NotificationServiceInterface $notificationService,
        RoleRepository $roleRepository)
    {
        $this->userRepositoryInterface = $userRepositoryInterface;
        $this->passwordHasher = $passwordHasher;
        $this->validator = $validator;
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->notificationService = $notificationService;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @throws \DateMalformedStringException
     * @throws RandomException
     * @throws \Exception
     */
    public function register(CreateUserRequest $data): User
    {
        // En tu UseCase o al inicio del proceso:
        $existingUser = $this->userRepositoryInterface->findByEmail($data->getEmail());
        if ($existingUser) {
            // Significa que ya hay un email igual en la BBDD.
            // Puedes lanzar una excepción controlada o devolver un error 400
            throw new \RuntimeException('EMAIL_IN_USE');
        }
        $user = User::fromCreateUserRequest($data, $this->passwordHasher,$this->roleRepository);
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

            // $this->notificationService->sendEmailVerificationToUser($user);
            // $this->notificationService->sendEmailToBack($user);

            // $this->entityManager->commit();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
