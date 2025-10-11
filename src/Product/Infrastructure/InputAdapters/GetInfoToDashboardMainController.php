<?php

namespace App\Product\Infrastructure\InputAdapters;

use App\Product\Application\DTO\GetInfoToDashboardMainRequest;
use App\Product\Application\InputPorts\GetInfoToDashboardMainUseCaseInterface;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetInfoToDashboardMainController extends AbstractController
{
    use PermissionControllerTrait;

    private GetInfoToDashboardMainUseCaseInterface $getInfoToDashboardMainUseCase;
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger,
        GetInfoToDashboardMainUseCaseInterface $getInfoToDashboardMainUseCase,
        PermissionService $permissionService
    ) {
        $this->getInfoToDashboardMainUseCase = $getInfoToDashboardMainUseCase;
        $this->logger = $logger;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/product_dashboard', name: 'api_product_dashboard', methods: ['POST'])]
    #[OA\Post(
        path: '/api/product_dashboard',
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
        tags: ['Product'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Información de los productos devueltos con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'product',
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
                                        property: 'name',
                                        type: 'string',
                                        example: 'Producto2'
                                    ),
                                    new OA\Property(
                                        property: 'stock_kg',
                                        type: 'number',
                                        example: 5.5
                                    ),
                                    new OA\Property(
                                        property: 'stock_in_units',
                                        type: 'number',
                                        example: 2.75
                                    ),
                                    new OA\Property(
                                        property: 'real_weight_sum_kg',
                                        type: 'number',
                                        example: 3.351
                                    ),
                                    new OA\Property(
                                        property: 'real_weight_sum_in_units',
                                        type: 'number',
                                        example: 1.68
                                    ),
                                    new OA\Property(
                                        property: 'percentage',
                                        type: 'string',
                                        example: '60.93%'
                                    ),
                                    new OA\Property(
                                        property: 'unit',
                                        properties: [
                                            new OA\Property(
                                                property: 'main_unit',
                                                type: 'integer',
                                                example: 1
                                            ),
                                            new OA\Property(
                                                property: 'unit_name',
                                                type: 'string',
                                                example: 'Paquetes'
                                            ),
                                            new OA\Property(
                                                property: 'conversion_factor',
                                                type: 'number',
                                                example: 2
                                            ),
                                        ],
                                        type: 'object'
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
    public function getProductsInfoToDashboardMain(Request $request): JsonResponse
    {
        // Modern permission check - replace the old role checks
        $permissionCheck = $this->checkPermissionJson('product.dashboard', 'No tienes permisos para ver el dashboard');
        if ($permissionCheck) {
            return $permissionCheck;
        }

        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;

        if (!$uuidClient) {
            $this->logger->warning('ProductController: uuidClient no proporcionado.');

            return new JsonResponse(['message' => 'CLIENT_NOT_FOUND'], Response::HTTP_UNAUTHORIZED);
        }

        // Validación opcional del formato UUID
        if (!$this->isValidUuid($uuidClient)) {
            $this->logger->warning('ProductController: uuidClient con formato inválido.');

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
            $this->logger->warning('ProductController: Usuario no tiene acceso al cliente proporcionado.');

            return new JsonResponse(['error' => 'Access denied to the specified client'], 403);
        }

        try {
            // Crear el DTO de solicitud y ejecutar el caso de uso
            $dashboardRequest = new GetInfoToDashboardMainRequest($uuidClient);
            $response = $this->getInfoToDashboardMainUseCase->execute($dashboardRequest);

            if ($response->getError()) {
                return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
            }

            return new JsonResponse(['product' => $response->getProduct()], $response->getStatusCode());
        } catch (\Exception $e) {
            $this->logger->error('ProductController: Error al obtener el producto.', ['exception' => $e]);

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
