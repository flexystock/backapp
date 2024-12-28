<?php

namespace App\EventSubscriber;

use App\Entity\Client\ApiCallsLog;
use App\Product\Infrastructure\OutputAdapters\Services\ClientConnectionManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class RequestLogSubscriber implements EventSubscriberInterface
{
    private RequestStack $requestStack;
    private Security $security;
    private ClientConnectionManager $clientConnectionManager;
    private LoggerInterface $logger;
    private ?Request $masterRequest = null;

    public function __construct(RequestStack $requestStack, Security $security, ClientConnectionManager $clientConnectionManager, LoggerInterface $logger)
    {
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->clientConnectionManager = $clientConnectionManager;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 100],
            TerminateEvent::class => 'onKernelTerminate',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($event->isMainRequest()) {
            $this->masterRequest = $event->getRequest();
            $this->masterRequest->attributes->set('_start_time', microtime(true));
        }
    }

    public function onKernelTerminate(TerminateEvent $event): void
    {
        $this->logger->info('onKernelTerminate: Iniciando el registro de la petición.');

        // Si no tenemos una masterRequest, salimos
        if (!$this->masterRequest) {
            $this->logger->info('onKernelTerminate: No hay masterRequest, salimos.');

            return;
        }

        $request = $this->masterRequest;

        // Solo logear si la ruta empieza por /api
        if (!str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        // Excluir la ruta de login y api/doc
        if ('/api/login' === $request->getPathInfo()) {
            $this->logger->info('onKernelTerminate: La ruta es /api/login, no se registra.');

            return;
        }
        if ('/api/doc' === $request->getPathInfo()) {
            $this->logger->info('onKernelTerminate: La ruta es /api/doc, no se registra.');

            return;
        }
        if ('/api/user_register' === $request->getPathInfo()) {
            $this->logger->info('onKernelTerminate: La ruta es /api/user_register, no se registra.');

            return;
        }

        if ('/api/client_register' === $request->getPathInfo()) {
            $this->logger->info('onKernelTerminate: La ruta es /api/client_register, no se registra.');

            return;
        }
        if ('/api/device_register' === $request->getPathInfo()) {
            $this->logger->info('onKernelTerminate: La ruta es /api/device_register, no se registra.');

            return;
        }
        if ('/api/app_register' === $request->getPathInfo()) {
            $this->logger->info('onKernelTerminate: La ruta es /api/app_register, no se registra.');

            return;
        }
        if ('/api/clients' === $request->getPathInfo()) {
            $this->logger->info('onKernelTerminate: La ruta es /api/app_register, no se registra.');

            return;
        }
        if ('/api/devices' === $request->getPathInfo()) {
            $this->logger->info('onKernelTerminate: La ruta es /api/app_register, no se registra.');

            return;
        }
        if ('/api/user/clients' === $request->getPathInfo()) {
            $this->logger->info('onKernelTerminate: La ruta es /api/user/clients, no se registra.');

            return;
        }
        // Calcular tiempo de procesamiento
        $startTime = $request->attributes->get('_start_time');
        if (!$startTime) {
            $startTime = microtime(true);
        }
        $processingTime = microtime(true) - $startTime;

        $response = $event->getResponse();
        $httpCode = $response ? $response->getStatusCode() : 0;

        // Decodificar el JSON del request en array
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            $data = [];
        }

        // Obtener el uuid_client de la petición
        $uuidClientFromRequest = $data['uuidClient'] ?? null;

        $ip = $request->getClientIp() ?? 'unknown';
        $endpoint = $request->getPathInfo();

        $user = $this->security->getUser();
        $this->logger->info('onKernelTerminate: Usuario => '.($user ? $user->getEmail() : 'no user'));
        $this->logger->info('onKernelTerminate: Request Path => '.$endpoint);
        $this->logger->info('onKernelTerminate: Request Content => '.$request->getContent());
        $this->logger->info('onKernelTerminate: UUID del cliente de la petición => '.($uuidClientFromRequest ?: 'sin uuid'));

        // Como en la entidad ApiCallsLog, setRequestData es string, convertimos el array a JSON
        $requestDataString = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // El responseData lo podemos dejar tal cual (es un string)
        $responseData = $response ? $response->getContent() : '';

        try {
            // Obtener el EM del cliente usando el uuid_client de la petición
            $em = $this->clientConnectionManager->getEntityManager($uuidClientFromRequest);

            $log = new ApiCallsLog();
            $log->setRequestAt(new \DateTime());
            $log->setEndpoint($endpoint);
            $log->setIp($ip);
            $log->setProcessingTime((float) $processingTime);
            $log->setHttpCode($httpCode);
            $log->setRequestData($requestDataString); // Aquí usamos la versión string del requestData
            $log->setResponseData($responseData);

            $em->persist($log);
            $em->flush();
            $this->logger->info('Llegamos al final: onKernelTerminate se disparó y se registró la petición.');
        } catch (\Exception $e) {
            $this->logger->error('Error logging request', [
                'exception' => $e,
                'uuid_client' => $uuidClientFromRequest,
            ]);
        }
    }
}
