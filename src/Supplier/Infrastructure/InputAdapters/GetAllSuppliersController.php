<?php

namespace App\Supplier\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use App\Supplier\Application\DTO\GetAllSuppliersRequest;
use App\Supplier\Application\InputPorts\GetAllSuppliersUseCaseInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetAllSuppliersController extends AbstractController
{
    use PermissionControllerTrait;

    private GetAllSuppliersUseCaseInterface $getAllSuppliersUseCase;
    private LoggerInterface $logger;

    public function __construct(
        GetAllSuppliersUseCaseInterface $getAllSuppliersUseCase,
        LoggerInterface $logger,
        PermissionService $permissionService
    ) {
        $this->getAllSuppliersUseCase = $getAllSuppliersUseCase;
        $this->logger = $logger;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/suppliers', name: 'api_get_all_suppliers', methods: ['GET'])]
    #[RequiresPermission('supplier.view')]
    #[OA\Get(
        path: '/api/suppliers',
        summary: 'Obtener todos los proveedores disponibles (BBDD main)',
        tags: ['Supplier'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de proveedores devuelta con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'suppliers',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'Proveedor ABC'),
                                    new OA\Property(property: 'slug', type: 'string', example: 'proveedor-abc'),
                                    new OA\Property(property: 'logo_url', type: 'string', example: 'https://example.com/logo.png', nullable: true),
                                    new OA\Property(property: 'website', type: 'string', example: 'https://www.proveedor-abc.com', nullable: true),
                                    new OA\Property(property: 'category', type: 'string', enum: ['mayorista', 'distribuidor', 'fabricante', 'marketplace'], example: 'distribuidor'),
                                    new OA\Property(property: 'country', type: 'string', example: 'ES'),
                                    new OA\Property(property: 'coverage_area', type: 'string', example: 'Nacional', nullable: true),
                                    new OA\Property(property: 'description', type: 'string', example: 'Proveedor de productos alimentarios', nullable: true),
                                    new OA\Property(property: 'has_api_integration', type: 'boolean', example: false),
                                    new OA\Property(property: 'integration_type', type: 'string', example: 'email', nullable: true),
                                    new OA\Property(property: 'is_active', type: 'boolean', example: true),
                                    new OA\Property(property: 'featured', type: 'boolean', example: false),
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
                response: 403,
                description: 'Usuario no tiene permisos'
            ),
            new OA\Response(
                response: 500,
                description: 'Error interno del servidor'
            ),
        ]
    )]
    public function __invoke(): JsonResponse
    {
        try {
            $request = new GetAllSuppliersRequest();
            $response = $this->getAllSuppliersUseCase->execute($request);

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
            $this->logger->error('Error in GetAllSuppliersController', [
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
