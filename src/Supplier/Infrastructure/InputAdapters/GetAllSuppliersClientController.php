<?php

namespace App\Supplier\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use App\Supplier\Application\DTO\GetAllSuppliersClientRequest;
use App\Supplier\Application\InputPorts\GetAllSuppliersClientUseCaseInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetAllSuppliersClientController extends AbstractController
{
    use PermissionControllerTrait;

    private GetAllSuppliersClientUseCaseInterface $getAllSuppliersClientUseCase;
    private LoggerInterface $logger;

    public function __construct(
        GetAllSuppliersClientUseCaseInterface $getAllSuppliersClientUseCase,
        LoggerInterface $logger,
        PermissionService $permissionService
    ) {
        $this->getAllSuppliersClientUseCase = $getAllSuppliersClientUseCase;
        $this->logger = $logger;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/client/suppliers', name: 'api_get_all_suppliers_client', methods: ['POST'])]
    #[RequiresPermission('supplier.view')]
    #[OA\Post(
        path: '/api/client/suppliers',
        summary: 'Obtener proveedores asignados a un cliente',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid', example: 'c014a415-4113-49e5-80cb-cc3158c15236'),
                ],
                type: 'object'
            )
        ),
        tags: ['Supplier'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de proveedores del cliente devuelta con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'suppliers',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'supplier_id', type: 'integer', example: 5),
                                    new OA\Property(property: 'supplier_name', type: 'string', example: 'Proveedor ABC', nullable: true),
                                    new OA\Property(property: 'email', type: 'string', example: 'contacto@proveedor.com', nullable: true),
                                    new OA\Property(property: 'phone', type: 'string', example: '+34123456789', nullable: true),
                                    new OA\Property(property: 'contact_person', type: 'string', example: 'Juan Pérez', nullable: true),
                                    new OA\Property(property: 'delivery_days', type: 'integer', example: 2),
                                    new OA\Property(property: 'address', type: 'string', example: 'Calle Principal 123', nullable: true),
                                    new OA\Property(property: 'integration_enabled', type: 'boolean', example: false),
                                    new OA\Property(property: 'integration_config', type: 'object', nullable: true),
                                    new OA\Property(property: 'notes', type: 'string', example: 'Proveedor preferido', nullable: true),
                                    new OA\Property(property: 'internal_code', type: 'string', example: 'PROV-001', nullable: true),
                                    new OA\Property(property: 'is_active', type: 'boolean', example: true),
                                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2023-12-13 10:30:00'),
                                    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2023-12-13 10:30:00'),
                                ],
                                type: 'object'
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Parámetros inválidos'
            ),
            new OA\Response(
                response: 403,
                description: 'Usuario no tiene permisos'
            ),
            new OA\Response(
                response: 404,
                description: 'Cliente no encontrado'
            ),
            new OA\Response(
                response: 500,
                description: 'Error interno del servidor'
            ),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['uuidClient'])) {
                return new JsonResponse(
                    ['error' => 'MISSING_UUID_CLIENT'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $useCaseRequest = new GetAllSuppliersClientRequest($data['uuidClient']);
            $response = $this->getAllSuppliersClientUseCase->execute($useCaseRequest);

            if ($response->getError()) {
                return new JsonResponse(
                    ['error' => $response->getError()],
                    $response->getStatusCode()
                );
            }

            return new JsonResponse(
                ['suppliers' => $response->getSuppliers()],
                $response->getStatusCode()
            );

        } catch (\Exception $e) {
            $this->logger->error('Error in GetAllSuppliersClientController', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return new JsonResponse(
                ['error' => 'INTERNAL_SERVER_ERROR'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
