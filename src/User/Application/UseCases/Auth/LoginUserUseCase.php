<?php

namespace App\User\Application\UseCases\Auth;

use App\Entity\Main\Login;
use App\Entity\Main\User;
use App\User\Application\InputPorts\Auth\LoginUserInputPort;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginUserUseCase implements LoginUserInputPort
{
    private UserRepositoryInterface $userRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;

    public function __construct(UserRepositoryInterface $userRepository, UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    /**
     * Authenticate a user against stored credentials.
     *
     * @param string $email     email address provided by the user
     * @param string $password  plain password provided by the user
     * @param string $ipAddress IP address of the login attempt
     *
     * @return User|null authenticated user or null on failure
     */
    public function login(string $email, string $password, string $ipAddress): ?User
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            return null;
        }

        if ($user->getLockedUntil() && $user->getLockedUntil() > new \DateTimeImmutable()) {
            return null;
        }

        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            $this->incrementFailedAttempts($user);

            return null;
        }

        $this->resetFailedAttempts($user);
        
        // Update last_access field with current datetime
        $user->setLastAccess(new \DateTime());
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        if ($user->isActive()) {
            $this->registerLogin($user, $ipAddress);
        }

        return $user;
    }

    /**
     * Registra un nuevo intento de login en la base de datos.
     *
     * Este método crea un nuevo registro en la tabla `login`, almacenando el UUID del usuario,
     * la fecha y hora del login, y la dirección IP desde la cual se realizó el intento de login.
     *
     * @param User   $user      la entidad del usuario que se ha logueado
     * @param string $ipAddress la dirección IP desde la cual se realizó el intento de login
     */
    private function registerLogin(User $user, string $ipAddress): void
    {
        $loginRecord = new Login();
        $loginRecord->setUuidUser($user->getUuid());
        $loginRecord->setLoginAt(new \DateTimeImmutable());
        $loginRecord->setIpAddress($ipAddress);

        $this->entityManager->persist($loginRecord);
        $this->entityManager->flush();
    }

    /**
     * Provide a user friendly error message after a failed login attempt.
     *
     * @param User $user the user that attempted to log in
     *
     * @return string|null Message to display or null if account is not yet locked
     */
    public function handleFailedLogin(User $user): ?string
    {
        if ($user->getLockedUntil() && $user->getLockedUntil() > new \DateTimeImmutable()) {
            $lockedUntil = $user->getLockedUntil()->format('Y-m-d H:i:s');

            return "Se ha superado el número máximo de intentos. Su cuenta está bloqueada hasta: $lockedUntil.";
        }

        $remainingAttempts = $this->getRemainingAttempts($user->getFailedAttempts());

        if ($remainingAttempts > 0) {
            return "Credenciales incorrectas. Le quedan $remainingAttempts intentos antes de que su cuenta sea bloqueada.";
        }

        return null;
    }

    /**
     * Calculate how many attempts are left before locking the account.
     *
     * @param int $failedAttempts number of failed attempts so far
     *
     * @return int remaining attempts before the next lock threshold
     */
    private function getRemainingAttempts(int $failedAttempts): int
    {
        $criticalAttempts = [4, 7, 10, 13];
        foreach ($criticalAttempts as $threshold) {
            if ($failedAttempts < $threshold) {
                return $threshold - $failedAttempts;
            }
        }

        return 0;
    }

    /**
     * Incrementa el contador de intentos fallidos de login para un usuario.
     *
     * Si el número de intentos fallidos excede de 3, se bloquea la cuenta del usuario por 15 minutos.
     * El bloqueo se establece en el campo `lockedUntil`.
     *
     * @param User $user la entidad del usuario cuyo contador de intentos fallidos se va a incrementar
     */
    private function incrementFailedAttempts(User $user): void
    {
        $failedAttempts = $user->getFailedAttempts() + 1;
        $user->setFailedAttempts($failedAttempts);

        if ($failedAttempts > 3) {
            // Si es la primera vez que supera los 3 intentos fallidos
            if (4 == $failedAttempts) {
                $user->setLockedUntil((new \DateTimeImmutable())->modify('+15 minutes'));
            } elseif (7 == $failedAttempts) {
                $user->setLockedUntil((new \DateTimeImmutable())->modify('+1 hour'));
            } elseif (10 == $failedAttempts) {
                $user->setLockedUntil((new \DateTimeImmutable())->modify('+2 hour'));
            } elseif (13 == $failedAttempts) {
                $user->setLockedUntil((new \DateTimeImmutable())->modify('+5 hour'));
            }
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * Restablece el contador de intentos fallidos de login para un usuario.
     *
     * Este método establece el contador de intentos fallidos a 0 y desbloquea la cuenta si estaba bloqueada,
     * eliminando cualquier valor en el campo `lockedUntil`.
     *
     * @param User $user la entidad del usuario cuyo contador de intentos fallidos se va a restablecer
     */
    private function resetFailedAttempts(User $user): void
    {
        $user->setFailedAttempts(0);
        $user->setLockedUntil(null); // Desbloquear la cuenta en caso de que esté bloqueada
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
