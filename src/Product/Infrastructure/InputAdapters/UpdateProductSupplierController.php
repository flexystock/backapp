<?php

namespace App\Product\Infrastructure\InputAdapters;

use App\Product\Application\DTO\UpdateProductSupplierRequest;
use App\Product\Application\InputPorts\UpdateProductSupplierUseCaseInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateProductSupplierController extends AbstractController
{
    private UpdateProductSupplierUseCaseInterface $updateProductSupplierUseCase;
    private LoggerInterface $logger;

    public function __construct(
        UpdateProductSupplierUseCaseInterface $updateProductSupplierUseCase,
        LoggerInterface $logger
    ) {
        $this->updateProductSupplierUseCase = $updateProductSupplierUseCase;
        $this->logger = $logger;
    }

    #[Route('/api/product/supplier', name: 'update_product_supplier', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/product/supplier',
        summary: 'Assign or update a supplier for a product',
        tags: ['Product Suppliers']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['uuidClient', 'productId', 'clientSupplierId'],
            properties: [
                new OA\Property(property: 'uuidClient', type: 'string', example: 'c014a415-4113-49e5-80cb-cc3158c15236'),
                new OA\Property(property: 'productId', type: 'integer', example: 1),
                new OA\Property(property: 'clientSupplierId', type: 'integer', example: 1),
                new OA\Property(property: 'isPreferred', type: 'boolean', example: true),
                new OA\Property(property: 'productCode', type: 'string', nullable: true, example: 'PROD-001'),
                new OA\Property(property: 'unitPrice', type: 'number', format: 'float', nullable: true, example: 12.50),
                new OA\Property(property: 'minOrderQuantity', type: 'number', format: 'float', nullable: true, example: 10),
                new OA\Property(property: 'deliveryDays', type: 'integer', nullable: true, example: 2),
                new OA\Property(property: 'notes', type: 'string', nullable: true, example: 'Proveedor preferido'),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Product supplier assigned/updated successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'productSupplier',
                    type: 'object',
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'product_id', type: 'integer', example: 1),
                        new OA\Property(property: 'client_supplier_id', type: 'integer', example: 1),
                        new OA\Property(property: 'is_preferred', type: 'boolean', example: true),
                        new OA\Property(property: 'product_code', type: 'string', nullable: true, example: 'PROD-001'),
                        new OA\Property(property: 'unit_price', type: 'number', format: 'float', nullable: true, example: 12.50),
                        new OA\Property(property: 'min_order_quantity', type: 'number', format: 'float', nullable: true, example: 10),
                        new OA\Property(property: 'delivery_days', type: 'integer', nullable: true, example: 2),
                        new OA\Property(property: 'notes', type: 'string', nullable: true, example: 'Proveedor preferido'),
                        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Invalid request data')]
    #[OA\Response(response: 404, description: 'Product, client or supplier not found')]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Validate required fields
            if (!isset($data['uuidClient']) || !isset($data['productId']) || !isset($data['clientSupplierId'])) {
                return new JsonResponse(
                    ['error' => 'Missing required fields: uuidClient, productId, clientSupplierId'],
                    Response::HTTP_BAD_REQUEST
                );
            }

            // Create request DTO
            $updateRequest = new UpdateProductSupplierRequest(
                $data['uuidClient'],
                (int) $data['productId'],
                (int) $data['clientSupplierId'],
                $data['isPreferred'] ?? false,
                $data['productCode'] ?? null,
                isset($data['unitPrice']) ? (float) $data['unitPrice'] : null,
                isset($data['minOrderQuantity']) ? (float) $data['minOrderQuantity'] : null,
                isset($data['deliveryDays']) ? (int) $data['deliveryDays'] : null,
                $data['notes'] ?? null
            );

            // Execute use case
            $response = $this->updateProductSupplierUseCase->execute($updateRequest);

            return new JsonResponse($response->toArray(), Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            $this->logger->error('[UpdateProductSupplierController] Runtime error', [
                'error' => $e->getMessage(),
            ]);

            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            $this->logger->error('[UpdateProductSupplierController] Unexpected error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return new JsonResponse(
                ['error' => 'An error occurred while assigning supplier to product'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
