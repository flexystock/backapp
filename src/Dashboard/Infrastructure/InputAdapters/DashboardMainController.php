<?php

namespace App\Dashboard\Infrastructure\InputAdapters;

use App\Dashboard\Application\DTO\GetDashboardSummaryRequest;
use App\Dashboard\Application\InputPorts\GetDashboardSummaryUseCaseInterface;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardMainController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly GetDashboardSummaryUseCaseInterface $getDashboardSummaryUseCase,
        PermissionService $permissionService
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/dashboard', name: 'dashboard_summary', methods: ['POST'])]
    #[OA\Post(
        path: '/dashboard',
        summary: 'Obtiene un resumen del dashboard principal',
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
        tags: ['Dashboard'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Resumen agregado del dashboard',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'lowStockProducts',
                            type: 'array',
                            items: new OA\Items(type: 'object')
                        ),
                        new OA\Property(
                            property: 'lowBatteryScales',
                            type: 'array',
                            items: new OA\Items(type: 'object')
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Solicitud inválida',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'uuidClient is required')
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'No autenticado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'USER_NOT_AUTHENTICATED')
                    ]
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Acceso denegado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'No tienes permisos para ver el dashboard')
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Información no encontrada',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Product not found')
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Error interno del servidor',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Internal Server Error')
                    ]
                )
            ),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        if ($response = $this->checkPermissionJson('product.dashboard', 'No tienes permisos para ver el dashboard de productos')) {
            return $response;
        }

        if ($response = $this->checkPermissionJson('scale.dashboard', 'No tienes permisos para ver el dashboard de balanzas')) {
            return $response;
        }

        $data = json_decode($request->getContent(), true) ?? [];
        $uuidClient = $data['uuidClient'] ?? null;

        if (!$uuidClient) {
            return new JsonResponse(['error' => 'uuidClient is required'], Response::HTTP_BAD_REQUEST);
        }

        if (!$this->isValidUuid($uuidClient)) {
            return new JsonResponse(['error' => 'CLIENT_NOT_FOUND'], Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['message' => 'USER_NOT_AUTHENTICATED'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user->getClients()->exists(fn ($key, $client) => $client->getUuidClient() === $uuidClient)) {
            return new JsonResponse(['message' => 'Access denied to the specified client'], Response::HTTP_FORBIDDEN);
        }

        try {
            $dashboardRequest = new GetDashboardSummaryRequest($uuidClient);
            $summaryResponse = $this->getDashboardSummaryUseCase->execute($dashboardRequest);

            if (Response::HTTP_OK !== $summaryResponse->getStatusCode()) {
                return new JsonResponse(
                    ['error' => $summaryResponse->getError() ?? 'Unexpected error'],
                    $summaryResponse->getStatusCode()
                );
            }

            return new JsonResponse(
                [
                    'lowStockProducts' => $summaryResponse->getLowStockProducts(),
                    'lowBatteryScales' => $summaryResponse->getLowBatteryScales(),
                ],
                Response::HTTP_OK
            );
        } catch (\Throwable $exception) {
            $this->logger->error('DashboardMainController: error while building dashboard summary', [
                'exception' => $exception,
            ]);

            return new JsonResponse(['error' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function isValidUuid(string $uuid): bool
    {
        return 1 === preg_match(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/',
            $uuid
        );
    }
}
