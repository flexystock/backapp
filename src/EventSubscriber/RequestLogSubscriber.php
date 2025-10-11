<?php

namespace App\EventSubscriber;

use App\Entity\Client\ApiCallsLog;
use App\Infrastructure\Services\ClientConnectionManager;
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
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly Security $security,
        private readonly ClientConnectionManager $clientConnectionManager,
        private readonly LoggerInterface $logger,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST   => ['onKernelRequest', 100],
            KernelEvents::TERMINATE => 'onKernelTerminate',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        // Marcamos el inicio para medir tiempo
        $event->getRequest()->attributes->set('_start_time', microtime(true));
    }

    public function onKernelTerminate(TerminateEvent $event): void
    {
        $request = $event->getRequest();

        // Solo main request
        if (!$request->attributes->getBoolean('_controller') && !$request->attributes->has('_start_time')) {
            // Si no es main o no pasó por onKernelRequest, seguimos pero marcamos inicio ahora
            $request->attributes->set('_start_time', microtime(true));
        }

        // Filtrado de endpoints
        $path = $request->getPathInfo();
        if (!$this->shouldLogPath($path)) {
            return;
        }

        // Tiempo de procesamiento
        $startTime = $request->attributes->get('_start_time', microtime(true));
        $processingTime = microtime(true) - (float) $startTime;

        $response = $event->getResponse();
        $httpCode = $response?->getStatusCode() ?? 0;

        // Construimos payload de forma segura (query + body JSON si hay)
        $payload = $this->buildPayload($request);

        // uuidClient desde query/body/atributos de ruta
        $uuidClient = $this->extractUuidClient($request, $payload);

        // Si no hay uuidClient, evitamos llamar a ClientConnectionManager (suele requerirlo)
        if (!$uuidClient) {
            $this->logger->info('RequestLog: no uuidClient, se omite persistencia específica del cliente.', [
                'path' => $path,
            ]);
            return;
        }

        // Datos de request/response
        $requestDataString = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $responseData = $response?->getContent() ?? '';

        $ip = $request->getClientIp() ?? 'unknown';
        $user = $this->security->getUser();

        $this->logger->info('RequestLog: registrando llamada', [
            'user'         => $user?->getUserIdentifier() ?? 'anonymous',
            'path'         => $path,
            'http_code'    => $httpCode,
            'processing_s' => round($processingTime, 4),
            'uuid_client'  => $uuidClient,
        ]);

        try {
            $em = $this->clientConnectionManager->getEntityManager($uuidClient);

            $log = new ApiCallsLog();
            $log->setRequestAt(new \DateTimeImmutable());
            $log->setEndpoint($path);
            $log->setIp($ip);
            $log->setProcessingTime((float) $processingTime);
            $log->setHttpCode($httpCode);
            $log->setRequestData($requestDataString);
            $log->setResponseData($responseData);

            $em->persist($log);
            $em->flush();
        } catch (\Throwable $e) {
            $this->logger->error('RequestLog: error persistiendo log', [
                'exception'   => $e->getMessage(),
                'uuid_client' => $uuidClient,
                'path'        => $path,
            ]);
        }
    }

    /**
     * Indica si se debe loguear el path.
     */
    private function shouldLogPath(string $path): bool
    {
        if (!str_starts_with($path, '/api')) {
            return false;
        }

        // Rutas exactas a excluir
        $excludedExact = [
            '/api/login',
            '/api/user_register',
            '/api/client_register',
            '/api/device_register',
            '/api/app_register',
            '/api/clients',
            '/api/devices',
            '/api/user/clients',
            '/api/ttn-uplink',
            '/api/admin',
            '/api/create_subscription_plan',
            '/api/subscription_plans',
            '/api/subscription_plan_update',
            '/api/subscription_plan_delete',
            '/api/create_subscription',
            '/api/stripe/webhook',
            '/api/subscription/stripe_latest_invoice',
        ];

        if (in_array($path, $excludedExact, true)) {
            return false;
        }

        // Prefijos a excluir (por ejemplo, documentación)
        $excludedPrefixes = [
            '/api/doc',
        ];

        foreach ($excludedPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Construye el payload combinando query y body JSON (si hay).
     */
    private function buildPayload(Request $request): array
    {
        $payload = $request->query->all();

        $contentType = (string) $request->headers->get('Content-Type', '');
        $raw = $request->getContent(); // Symfony lo cachea; llamadas repetidas son seguras

        if ($raw !== '' && str_contains($contentType, 'application/json')) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                // Lo del body tiene preferencia sobre query
                $payload = array_merge($payload, $decoded);
            }
        }

        return $payload;
    }

    /**
     * Intenta obtener uuidClient desde diferentes fuentes.
     */
    private function extractUuidClient(Request $request, array $payload): ?string
    {
        $uuid = $payload['uuidClient'] ?? null;
        if (is_string($uuid) && $uuid !== '') {
            return $uuid;
        }

        // Atributos de la ruta (por si defines {uuidClient} en la ruta)
        $attrUuid = $request->attributes->get('uuidClient');
        if (is_string($attrUuid) && $attrUuid !== '') {
            return $attrUuid;
        }

        return null;
    }
}
