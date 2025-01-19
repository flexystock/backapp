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
        $user = $this->userRepository->findOneByVerificationToken($token);
        if (!$user) {
            return new Response('INVALID_LINK', Response::HTTP_BAD_REQUEST);
        }
        if ($user->isVerified()) {
            return new Response('YOUR_ACCOUNT_IS_ALREADY_VERIFIED', Response::HTTP_BAD_REQUEST);
        }
        if ($user->getVerificationTokenExpiresAt() < new \DateTime()) {
            $resendEmail = $this->resendEmailVerificationToken->resendEmailVerificationToken($user, $token);
            if (!$resendEmail) {
                return new Response('ERROR_RESENDING_EMAIL', Response::HTTP_BAD_REQUEST);
            }

            return new Response('LINK_TO_VERIFY_EXPIRED.NEW_LINK_SENT', Response::HTTP_BAD_REQUEST);
        }

        $clients = $user->getClients();

        if ($clients->isEmpty()) {
            return new Response('NO_CLIENT_ASSOCIATED', Response::HTTP_BAD_REQUEST);
        }

        $client = $clients->first();

        if (!$client) {
            return new Response('NO_CLIENT_ASSOCIATED', Response::HTTP_BAD_REQUEST);
        }

        $this->bus->dispatch(new CreateDockerContainerMessage($client->getUuidClient()));
        $user->setIsVerified(true);
        $user->setVerificationToken(null);
        $user->setVerificationTokenExpiresAt(null);
        $this->userRepository->save($user);

        return new Response('YOUR_ACCOUNT_IS_VERIFIED', Response::HTTP_OK);
    }

    #[Route('/verify_client/{token}', name: 'client_verification')]
    public function verifyClientUserEmail(string $token): Response
    {
        $user = $this->userRepository->findOneByVerificationToken($token);
        if (!$user) {
            return new Response('INVALID_LINK', Response::HTTP_BAD_REQUEST);
        }

        if ($user->getVerificationTokenExpiresAt() < new \DateTime()) {
            $resendEmail = $this->resendEmailVerificationToken->resendEmailVerificationToken($user, $token);
            if (!$resendEmail) {
                return new Response('ERROR_RESENDING_EMAIL', Response::HTTP_BAD_REQUEST);
            }

            return new Response('LINK_TO_VERIFY_EXPIRED.NEW_LINK_SENT', Response::HTTP_BAD_REQUEST);
        }

        $clients = $user->getClients();

        if ($clients->isEmpty()) {
            return new Response('NO_CLIENT_ASSOCIATED', Response::HTTP_BAD_REQUEST);
        }

        $client = $clients->last();

        if (!$client) {
            return new Response('NO_CLIENT_ASSOCIATED', Response::HTTP_BAD_REQUEST);
        }

        $this->bus->dispatch(new CreateDockerContainerMessage($client->getUuidClient()));
        $user->setIsVerified(true);
        $user->setVerificationToken(null);
        $user->setVerificationTokenExpiresAt(null);
        $this->userRepository->save($user);

        return new Response('YOUR_ACCOUNT_IS_VERIFIED', Response::HTTP_OK);
    }
}
