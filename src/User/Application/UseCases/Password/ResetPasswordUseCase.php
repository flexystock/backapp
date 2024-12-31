<?php

namespace App\User\Application\UseCases\Password;

use App\User\Application\DTO\Password\ResetPasswordRequest;
use App\User\Application\InputPorts\ResetPasswordInterface;
use App\User\Application\OutputPorts\NotificationServiceInterface;
use App\User\Application\OutputPorts\PasswordResetRepositoryInterface;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Caso de uso para restablecer la contraseña de un usuario.
 */
class ResetPasswordUseCase implements ResetPasswordInterface
{
    private UserRepositoryInterface $userRepository;
    private PasswordResetRepositoryInterface $passwordResetRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private NotificationServiceInterface $email;

    public function __construct(UserRepositoryInterface $userRepository,
        PasswordResetRepositoryInterface $passwordResetRepository,
        UserPasswordHasherInterface $passwordHasher,
        NotificationServiceInterface $email)
    {
        $this->userRepository = $userRepository;
        $this->passwordResetRepository = $passwordResetRepository;
        $this->email = $email;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Restablece la contraseña de un usuario utilizando un token de verificación.
     *
     * Este métod valida el token de restablecimiento de contraseña proporcionado,
     * actualiza la contraseña del usuario si el token es válido y elimina el registro
     * de restablecimiento de contraseña utilizado.
     *
     * @param ResetPasswordRequest $request objeto que contiene el email del usuario, el token y la nueva contraseña
     *
     * @throws \Exception si el token es inválido o ha expirado, o si el usuario no es encontrado
     */
    public function resetPassword(ResetPasswordRequest $request): void
    {
        $passwordReset = $this->passwordResetRepository->findByEmail($request->email);

        // Verificar si existe el registro de restablecimiento de contraseña
        if (!$passwordReset) {
            throw new \Exception('INVALID_TOKEN_OR_EXPIRED');
        }

        // Incrementar el contador de intentos
        $passwordReset->incrementAttempts();

        // Verificar si se ha excedido el número máximo de intentos
        $maxAttempts = 3;
        if ($passwordReset->hasExceededMaxAttempts($maxAttempts)) {
            // Eliminar el registro de restablecimiento
            $this->passwordResetRepository->remove($passwordReset);
            throw new \Exception('MAX_ATTEMPTS_EXCEEDED');
        }

        // Guardar el incremento de intentos en la base de datos
        $this->passwordResetRepository->save($passwordReset);

        // Verificar el token
        if (!$passwordReset->verifyToken($request->token)) {
            throw new \Exception('INVALID_TOKEN_OR_EXPIRED');
        }
        // Buscar al usuario por email
        $user = $this->userRepository->findByEmail($request->email);

        if (!$user) {
            throw new \Exception('USER_NOT_FOUND');
        }
        // Actualizar la contraseña
        $hashedPassword = $this->passwordHasher->hashPassword($user, $request->newPassword);
        $user->setPassword($hashedPassword);

        $this->userRepository->save($user);

        // Eliminar el registro de restablecimiento de contraseña
        $this->passwordResetRepository->remove($passwordReset);
        // Enviamos email email de confirmacion de reseteo de password
        $this->email->sendSuccesfullPasswordResetEmail($user);
    }
}
