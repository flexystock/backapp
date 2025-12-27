<?php

namespace App\Supplier\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use App\Supplier\Application\DTO\UpdateSupplierRequest;
use App\Supplier\Application\InputPorts\UpdateSupplierUseCaseInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateSupplierController extends AbstractController
{
    use PermissionControllerTrait;

    private UpdateSupplierUseCaseInterface $updateSupplierUseCase;
    private LoggerInterface $logger;

    public function __construct(
        UpdateSupplierUseCaseInterface $updateSupplierUseCase,
        LoggerInterface $logger,
        PermissionService $permissionService
    ) {
        $this->updateSupplierUseCase = $updateSupplierUseCase;
        $this->logger = $logger;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/supplier/{id}', name: 'api_update_supplier', methods: ['PUT'])]
    #[RequiresPermission('supplier.update')]
    #[OA\Put(
        path: '/api/supplier/{id}',
        summary: 'Actualizar un proveedor global en la BBDD main',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'slug'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Proveedor ABC'),
                    new OA\Property(property: 'slug', type: 'string', example: 'proveedor-abc'),
                    new OA\Property(property: 'logoUrl', type: 'string', example: 'https://example.com/logo.png', nullable: true),
                    new OA\Property(property: 'website', type: 'string', example: 'https://www.proveedor-abc.com', nullable: true),
                    new OA\Property(property: 'category', type: 'string', enum: ['mayorista', 'distribuidor', 'fabricante', 'marketplace'], example: 'distribuidor'),
                    new OA\Property(property: 'country', type: 'string', example: 'ES'),
                    new OA\Property(property: 'coverageArea', type: 'string', example: 'Nacional', nullable: true),
                    new OA\Property(property: 'description', type: 'string', example: 'Proveedor de productos alimentarios', nullable: true),
                    new OA\Property(property: 'hasApiIntegration', type: 'boolean', example: false),
                    new OA\Property(property: 'integrationType', type: 'string', example: 'email', nullable: true),
                    new OA\Property(property: 'isActive', type: 'boolean', example: true),
                    new OA\Property(property: 'featured', type: 'boolean', example: false),
                ],
                type: 'object'
            )
        ),
        tags: ['Supplier'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID del proveedor a actualizar',
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Proveedor actualizado con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'supplier',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Proveedor ABC'),
                                new OA\Property(property: 'slug', type: 'string', example: 'proveedor-abc'),
                                new OA\Property(property: 'category', type: 'string', example: 'distribuidor'),
                                new OA\Property(property: 'country', type: 'string', example: 'ES'),
                                new OA\Property(property: 'is_active', type: 'boolean', example: true),
                                new OA\Property(property: 'featured', type: 'boolean', example: false),
                            ]
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Parámetros inválidos o slug duplicado'
            ),
            new OA\Response(
                response: 403,
                description: 'Usuario no tiene permisos'
            ),
            new OA\Response(
                response: 404,
                description: 'Proveedor no encontrado'
            ),
            new OA\Response(
                response: 500,
                description: 'Error interno del servidor'
            ),
        ]
    )]
    public function __invoke(Request $request, int $id): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['name']) || !isset($data['slug'])) {
                return new JsonResponse(
                    ['error' => 'MISSING_REQUIRED_FIELDS'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            $useCaseRequest = new UpdateSupplierRequest(
                $id,
                $data['name'],
                $data['slug'],
                $data['logoUrl'] ?? null,
                $data['website'] ?? null,
                $data['category'] ?? 'distribuidor',
                $data['country'] ?? 'ES',
                $data['coverageArea'] ?? null,
                $data['description'] ?? null,
                $data['hasApiIntegration'] ?? false,
                $data['integrationType'] ?? null,
                $data['isActive'] ?? true,
                $data['featured'] ?? false
            );

            $response = $this->updateSupplierUseCase->execute($useCaseRequest);

            if ($response->getError()) {
                return new JsonResponse(
                    ['error' => $response->getError()],
                    $response->getStatusCode()
                );
            }

            return new JsonResponse(
                ['supplier' => $response->getSupplier()],
                $response->getStatusCode()
            );

        } catch (\Exception $e) {
            $this->logger->error('Error in UpdateSupplierController', [
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
