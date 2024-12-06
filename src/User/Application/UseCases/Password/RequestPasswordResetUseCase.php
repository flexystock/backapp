<?php

namespace App\User\Application\UseCases\Password;

use App\Entity\Main\PasswordReset;
use App\User\Application\DTO\Password\ForgotPasswordRequest;
use App\User\Application\InputPorts\RequestPasswordResetInterface;
use App\User\Application\OutputPorts\NotificationServiceInterface;
use App\User\Application\OutputPorts\PasswordResetRepositoryInterface;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;

/**
 * Caso de uso para solicitar el restablecimiento de contraseña.
 */
class RequestPasswordResetUseCase implements RequestPasswordResetInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordResetRepositoryInterface $passwordResetRepository,
        private NotificationServiceInterface $emailService,
    ) {
    }

    /**
     * Maneja la solicitud de restablecimiento de contraseña.
     *
     * Este métod procesa una solicitud para restablecer la contraseña de un usuario.
     * Si el email proporcionado existe en el sistema, genera un token de restablecimiento,
     * almacena el token y envía un correo electrónico al usuario con las instrucciones
     * para restablecer su contraseña.
     *
     * @param ForgotPasswordRequest $request objeto que contiene el email del usuario
     */
    public function requestPasswordReset(ForgotPasswordRequest $request): void
    {
        // Buscar al usuario por su email.
        $user = $this->userRepository->findByEmail($request->email);

        if (!$user) {
            // Si el usuario no existe, no revelar esta información por razones de seguridad.
            // Simplemente retornar sin realizar ninguna acción adicional.
            return;
        }
        // Eliminar cualquier registro previo de restablecimiento de contraseña para este email.
        $this->passwordResetRepository->removeAllByEmail($user->getEmail());
        // Generar un token aleatorio para el restablecimiento de contraseña.
        $token = bin2hex(random_bytes(3)); // 6 caracteres hexadecimales
        // Establecer la fecha y hora de expiración del token (ejemplo: +15 minutos desde ahora).
        $expiresAt = new \DateTimeImmutable('+15 minutes');
        // Crear una nueva entidad de PasswordReset con los datos necesarios.
        $passwordReset = new PasswordReset($user->getEmail(), $token, $expiresAt);
        // Guardar el registro de restablecimiento de contraseña en la base de datos.
        $this->passwordResetRepository->save($passwordReset);
        // Enviar un correo electrónico al usuario con el token de restablecimiento.
        $this->emailService->sendPasswordResetEmail($user, $token);
    }
}
