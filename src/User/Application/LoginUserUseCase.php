<?php

namespace App\User\Application;

use App\Entity\Main\User;
use App\Entity\Main\Login;
use App\User\Infrastructure\InputPorts\LoginUserInputPort;
use App\User\Infrastructure\OutputPorts\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

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

    public function login(string $mail, string $password, string $ipAddress): ?User
    {
        $user = $this->userRepository->findByEmail($mail);

        if (!$user) {
            return null;
        }
        // Actualizar el último acceso
        $user->setLastAccess(new \DateTimeImmutable());
        // Verificar si la cuenta está bloqueada
        if ($user->getLockedUntil() && $user->getLockedUntil() > new \DateTimeImmutable()) {
            return $user; // La cuenta está bloqueada
        }
        if (!$this->passwordHasher->isPasswordValid($user, $password)) {
            $this->incrementFailedAttempts($user);
            return $user; // Credenciales incorrectas
        }
        // Si el login es exitoso
        $this->resetFailedAttempts($user);
        // Registrar el login en la tabla 'login'
        $this->registerLogin($user,$ipAddress);
        return $user;
    }

    /**
     * Registra un nuevo intento de login en la base de datos.
     *
     * Este método crea un nuevo registro en la tabla `login`, almacenando el UUID del usuario,
     * la fecha y hora del login, y la dirección IP desde la cual se realizó el intento de login.
     *
     * @param User $user La entidad del usuario que se ha logueado.
     * @param string $ipAddress La dirección IP desde la cual se realizó el intento de login.
     * @return void
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

    public function handleFailedLogin(User $user): ?string
    {

        // Si el usuario ha sido bloqueado, devolver el mensaje de bloqueo
        if ($user->getLockedUntil()) {
            $lockedUntil = $user->getLockedUntil()->format('Y-m-d H:i:s');
            return "Intentos máximos alcanzados. Usuario bloqueado hasta: $lockedUntil.";
        }

        return null;
    }

    /**
     * Incrementa el contador de intentos fallidos de login para un usuario.
     *
     * Si el número de intentos fallidos excede de 3, se bloquea la cuenta del usuario por 15 minutos.
     * El bloqueo se establece en el campo `lockedUntil`.
     *
     * @param User $user La entidad del usuario cuyo contador de intentos fallidos se va a incrementar.
     * @return void
     */
    private function incrementFailedAttempts(User $user): void
    {
        $failedAttempts = $user->getFailedAttempts() + 1;
        $user->setFailedAttempts($failedAttempts);

        if ($failedAttempts > 3) {
            // Si es la primera vez que supera los 3 intentos fallidos
            if ($failedAttempts == 4) {
                $user->setLockedUntil((new \DateTimeImmutable())->modify('+15 minutes'));
            } else if ($failedAttempts == 7){
                $user->setLockedUntil((new \DateTimeImmutable())->modify('+1 hour'));
            }else if($failedAttempts == 10){
                $user->setLockedUntil((new \DateTimeImmutable())->modify('+2 hour'));
            }else if($failedAttempts == 13){
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
     * @param User $user La entidad del usuario cuyo contador de intentos fallidos se va a restablecer.
     * @return void
     */
    private function resetFailedAttempts(User $user): void
    {
        $user->setFailedAttempts(0);
        $user->setLockedUntil(null); // Desbloquear la cuenta en caso de que esté bloqueada
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

}