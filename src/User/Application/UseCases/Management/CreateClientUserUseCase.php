<?php

declare(strict_types=1);

namespace App\User\Application\UseCases\Management;

use App\Admin\Role\Infrastructure\OutputAdapters\Repositories\RoleRepository;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Main\User;
use App\User\Application\DTO\Management\CreateClientUserRequest;
use App\User\Application\InputPorts\CreateClientUserInputPort;
use App\User\Application\OutputPorts\NotificationServiceInterface;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateClientUserUseCase implements CreateClientUserInputPort
{
    private const DEFAULT_TIMEZONE = 'UTC';
    private const DEFAULT_LANGUAGE = 'es';

    private const RECOVER_PASSWORD_PATH = '/recoverPassword';

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly RoleRepository $roleRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly NotificationServiceInterface $notificationService,
        private readonly ValidatorInterface $validator,
        private readonly string $frontendBaseUrl,
    ) {
    }

    public function create(CreateClientUserRequest $request): User
    {
        $existingUser = $this->userRepository->findByEmail($request->getEmail());
        if (null !== $existingUser) {
            throw new \RuntimeException('EMAIL_IN_USE');
        }

        $client = $this->clientRepository->findByUuid($request->getUuidClient());
        if (null === $client) {
            throw new \RuntimeException('CLIENT_NOT_FOUND');
        }

        $role = $this->roleRepository->findByName($request->getRole());
        if (null === $role) {
            throw new \RuntimeException('ROLE_NOT_FOUND');
        }

        $user = new User();
        $user->setEmail($request->getEmail());
        $user->setName('Invited');
        $user->setSurnames('User');
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, bin2hex(random_bytes(16)))
        );
        $user->setTimeZone(self::DEFAULT_TIMEZONE);
        $user->setLanguage(self::DEFAULT_LANGUAGE);
        $user->setPreferredContactMethod('email');
        $user->setUuidUserCreation($request->getCreatedByUuid() ?? $user->getUuid());
        $user->setDatehourCreation(new \DateTimeImmutable());
        $user->setIsVerified(false);
        $user->addRole($role);
        $user->addClient($client);
        $user->setSelectedClientUuid($client->getUuidClient());

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            throw new \RuntimeException('INVALID_USER_DATA');
        }

        $this->userRepository->save($user);

        $forgotPasswordUrl = sprintf(
            '%s%s?email=%s',
            rtrim($this->frontendBaseUrl, '/'),
            self::RECOVER_PASSWORD_PATH,
            urlencode($user->getEmail())
        );
        $this->notificationService->sendNewUserInvitationEmail($user, $forgotPasswordUrl);

        return $user;
    }
}
