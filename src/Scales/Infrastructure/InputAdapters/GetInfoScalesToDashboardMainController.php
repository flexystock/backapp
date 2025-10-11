<?php

namespace App\Scales\Infrastructure\InputAdapters;

use App\Scales\Application\DTO\GetInfoScalesToDashboardMainRequest;
use App\Scales\Application\InputPorts\GetInfoScalesToDashboardMainUseCaseInterface;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use OpenApi\Attributes\OpenApi as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetInfoScalesToDashboardMainController extends AbstractController
{
    use PermissionControllerTrait;

    private GetInfoScalesToDashboardMainUseCaseInterface $getInfoScalesToDashboardMainUseCase;
    private LoggerInterface $logger;

    public function __construct(
        GetInfoScalesToDashboardMainUseCaseInterface $getInfoScalesToDashboardMainUseCase,
        LoggerInterface $logger,
        PermissionService $permissionService
    ) {
        $this->getInfoScalesToDashboardMainUseCase = $getInfoScalesToDashboardMainUseCase;
        $this->logger = $logger;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/scales_dashboard', name: 'api_scales_dashboard', methods: ['POST'])]
    #[RequiresPermission('scale.dashboard')]
    #[OA\Post(
        path: '/api/scales_dashboard',
        summary: 'Obtener información para el dashboard principal',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient'],
                properties: [
                    new OA\Property(
                        property: 'uuidClient',
                        type: 'string',
                        format: 'uuid',
                        example: 'c014a415-4113-49e5-80cb-cc3158c15236'
                    ),
                ],
                type: 'object'
            )
        ),
        tags: ['Scales'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Información de las balanzas devueltos con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'scale',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(
                                        property: 'uuid',
                                        type: 'string',
                                        format: 'uuid',
                                        example: '423ef69e-7cee-41a2-b26b-0a773e58a319'
                                    ),
                                    new OA\Property(
                                        property: 'end_device_id',
                                        type: 'string',
                                        example: 'heltec-ab01-4699'
                                    ),
                                    new OA\Property(
                                        property: 'voltage_min',
                                        type: 'number',
                                        example: 2.75
                                    ),
                                    new OA\Property(
                                        property: 'voltage_percentage',
                                        type: 'number',
                                        example: 100
                                    ),
                                    new OA\Property(
                                        property: 'last_send',
                                        type: 'date-time',
                                        example: '2025-06-14 09:41:14'
                                    ),
                                    new OA\Property(
                                        property: 'product_asociate',
                                        type: 'string',
                                        example: 'Galletas'
                                    ),
                                ],
                                type: 'object'
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Faltan "uuidClient" o el formato es inválido',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'error',
                            type: 'string',
                            example: 'uuidClient are required'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'No autenticado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'error',
                            type: 'string',
                            example: 'Unauthorized'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'El usuario no tiene acceso al cliente especificado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'error',
                            type: 'string',
                            example: 'Access denied to the specified client'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Error interno del servidor',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'error',
                            type: 'string',
                            example: 'Internal Server Error'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function getScalesInfoToDashboardMain(Request $request): JsonResponse
    {
        $permissionCheck = $this->checkPermissionJson('scale.dashboard', 'No tienes permisos para ver las balanzas');
        if ($permissionCheck) {
            return $permissionCheck;
        }
        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;

        if (!$uuidClient) {
            $this->logger->warning('ScalesController: uuidClient no proporcionado.');

            return new JsonResponse(['message' => 'CLIENT_NOT_FOUND'], Response::HTTP_UNAUTHORIZED);
        }

        // Validación opcional del formato UUID
        if (!$this->isValidUuid($uuidClient)) {
            $this->logger->warning('ScalesController: uuidClient con formato inválido.');

            return new JsonResponse(['message' => 'CLIENT_NOT_FOUND'], Response::HTTP_UNAUTHORIZED);
        }

        // Verificar que el usuario esté autenticado
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['message' => 'USER_NOT_AUTHENTICATED'], Response::HTTP_UNAUTHORIZED);
        }

        // Verificar que el usuario tenga acceso al cliente especificado
        if (!$user->getClients()->exists(function ($key, $client) use ($uuidClient) {
            return $client->getUuidClient() === $uuidClient;
        })) {
            $this->logger->warning('ScalesController: Usuario no tiene acceso al cliente proporcionado.');

            return new JsonResponse(['error' => 'Access denied to the specified client'], 403);
        }

        try {
            // Crear el DTO de solicitud y ejecutar el caso de uso
            $dashboardRequest = new GetInfoScalesToDashboardMainRequest($uuidClient);
            $response = $this->getInfoScalesToDashboardMainUseCase->execute($dashboardRequest);

            if ($response->getError()) {
                return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
            }

            return new JsonResponse(['scale' => $response->getScale()], $response->getStatusCode());
        } catch (\Exception $e) {
            $this->logger->error('ScaleController: Error al obtener la balanza.', ['exception' => $e]);

            return new JsonResponse(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Valida el formato UUID.
     */
    private function isValidUuid(string $uuid): bool
    {
        // Valida el formato UUID (versión 4 en este ejemplo)
        return 1 === preg_match(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/',
            $uuid
        );
    }
}
