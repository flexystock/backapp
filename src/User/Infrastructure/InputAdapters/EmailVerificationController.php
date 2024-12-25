<?php

namespace App\User\Infrastructure\InputAdapters;

use App\Message\CreateDockerContainerMessage;
use App\Service\DockerService;
use App\User\Application\InputPorts\Auth\ResendEmailVerificationTokenInterface;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class EmailVerificationController
{
    private UserRepositoryInterface $userRepository;
    private DockerService $dockerService;
    private MessageBusInterface $bus;
    private ResendEmailVerificationTokenInterface $resendEmailVerificationToken;

    public function __construct(UserRepositoryInterface $userRepository,
        DockerService $dockerService,
        MessageBusInterface $bus,
        ResendEmailVerificationTokenInterface $resendEmailVerificationToken)
    {
        $this->userRepository = $userRepository;
        $this->dockerService = $dockerService;
        $this->bus = $bus;
        $this->resendEmailVerificationToken = $resendEmailVerificationToken;
    }

    #[Route('/verify/{token}', name: 'user_verification')]
    public function verifyUserEmail(string $token): Response
    {
        // die("llegamos");
        $user = $this->userRepository->findOneByVerificationToken($token);
        if (!$user) {
            return new Response('El enlace de verificación no es válido.', Response::HTTP_BAD_REQUEST);
        }
        if ($user->isVerified()) {
            return new Response('Tu cuenta ya ha sido verificada.', Response::HTTP_OK);
        }
        if ($user->getVerificationTokenExpiresAt() < new \DateTime()) {
            $resendEmail = $this->resendEmailVerificationToken->resendEmailVerificationToken($user, $token);
            if (!$resendEmail) {
                return new Response('Error al reenviar el mail de verificacion.', Response::HTTP_BAD_REQUEST);
            }

            return new Response('El enlace de verificación ha expirado. Se le reenvió un nuevo enlace de verificación.', Response::HTTP_BAD_REQUEST);
        }

        $clients = $user->getClients();

        if ($clients->isEmpty()) {
            return new Response('No hay clientes asociados a este usuario.', Response::HTTP_BAD_REQUEST);
        }

        $client = $clients->first();

        if (!$client) {
            return new Response('No se pudo obtener el cliente asociado.', Response::HTTP_BAD_REQUEST);
        }

        $this->bus->dispatch(new CreateDockerContainerMessage($client->getUuidClient()));
        $user->setIsVerified(true);
        $user->setVerificationToken(null);
        $user->setVerificationTokenExpiresAt(null);
        $this->userRepository->save($user);

        return new Response('¡Tu cuenta ha sido verificada exitosamente!', Response::HTTP_OK);
    }
}
