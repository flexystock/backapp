<?php

namespace App\EventListener;

use App\Service\ClientConnectionProvider;
use App\Service\ClientEntityManagerFactory;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class ClientEmRequestListener
{
    private Security $security;
    private ClientConnectionProvider $connectionProvider;
    private ClientEntityManagerFactory $emFactory;
    private LoggerInterface $logger;

    public function __construct(
        Security $security,
        ClientConnectionProvider $connectionProvider,
        ClientEntityManagerFactory $emFactory,
        LoggerInterface $logger,
    ) {
        $this->security = $security;
        $this->connectionProvider = $connectionProvider;
        $this->emFactory = $emFactory;
        $this->logger = $logger;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $user = $this->security->getUser();
        if (!$user) {
            $this->logger->info('ClientEmRequestListener: No user authenticated.');
            return;
        }

        if (!method_exists($user, 'getSelectedClientUuid')) {
            $this->logger->error('ClientEmRequestListener: User entity does not have getSelectedClientUuid method.');
            throw new \RuntimeException('User entity does not have getSelectedClientUuid method.');
        }

        $uuidClient = $user->getSelectedClientUuid();

        if (!$uuidClient) {
            $this->logger->info('ClientEmRequestListener: No uuid_client assigned to user.');
            return; // No se seleccionó cliente todavía
        }

        try {
            $connectionParams = $this->connectionProvider->getConnectionParams($uuidClient);
            $clientEm = $this->emFactory->createEntityManagerForClient($connectionParams);
            $event->getRequest()->attributes->set('clientEm', $clientEm);
            $this->logger->info('ClientEmRequestListener: clientEm set in request attributes.');
        } catch (\Exception $e) {
            $this->logger->error('ClientEmRequestListener: Error setting clientEm.', ['exception' => $e]);
            throw $e; // Opcional: maneja la excepción según tus necesidades
        }
    }
}
