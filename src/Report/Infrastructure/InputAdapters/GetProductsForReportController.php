<?php

namespace App\Report\Infrastructure\InputAdapters;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\Product;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetProductsForReportController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly ClientRepositoryInterface $clientRepository,
        private readonly ClientConnectionManager $connectionManager,
        private readonly LoggerInterface $logger,
        PermissionService $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/report/products', name: 'api_report_products', methods: ['POST'])]
    #[OA\Post(
        path: '/api/report/products',
        summary: 'Obtiene todos los productos disponibles para crear informes específicos',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid', example: 'f7e98ceb-3839-4ea5-88ce-22b49c73b850'),
                ]
            )
        ),
        tags: ['Report'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Productos recuperados correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'PRODUCTS_RETRIEVED'),
                        new OA\Property(
                            property: 'products',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'Coca-Cola 2L'),
                                    new OA\Property(property: 'ean', type: 'string', example: '8410011010102'),
                                    new OA\Property(property: 'min_stock', type: 'number', example: 10),
                                ]
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Datos inválidos'),
            new OA\Response(response: 401, description: 'Usuario no autenticado'),
            new OA\Response(response: 403, description: 'Permisos insuficientes'),
            new OA\Response(response: 404, description: 'Cliente no encontrado'),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $permissionCheck = $this->checkPermissionJson('report.view', 'No tienes permisos para consultar productos');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            // Leer del body en lugar de query params
            $data = json_decode($request->getContent(), true);

            if (!is_array($data) || empty($data['uuidClient']) || !is_string($data['uuidClient'])) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'REQUIRED_CLIENT_ID',
                ], Response::HTTP_BAD_REQUEST);
            }

            $uuidClient = $data['uuidClient'];

            $client = $this->clientRepository->findByUuid($uuidClient);
            if (!$client) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'CLIENT_NOT_FOUND',
                ], Response::HTTP_NOT_FOUND);
            }

            $user = $this->getUser();
            if (!is_object($user) || !method_exists($user, 'getUuid')) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'USER_NOT_AUTHENTICATED',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $entityManager = $this->connectionManager->getEntityManager($client->getUuidClient());
            $productRepository = $entityManager->getRepository(Product::class);
            $products = $productRepository->findAll();

            $productsData = array_map(function (Product $product) {
                return [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'ean' => $product->getEan() ?? '',
                    'min_stock' => $product->getStock() ?? 0,
                ];
            }, $products);

            $this->logger->info('Products retrieved for reports', [
                'uuid_client' => $client->getUuidClient(),
                'count' => count($productsData),
            ]);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'PRODUCTS_RETRIEVED',
                'products' => $productsData,
            ], Response::HTTP_OK);
        } catch (\RuntimeException $exception) {
            return $this->handleRuntimeException($exception);
        } catch (\Throwable $throwable) {
            $this->logger->error('Unexpected error fetching products', [
                'exception' => $throwable->getMessage(),
            ]);

            return new JsonResponse([
                'status' => 'error',
                'message' => 'UNEXPECTED_ERROR',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function handleRuntimeException(\RuntimeException $exception): JsonResponse
    {
        $message = $exception->getMessage();
        $statusCode = Response::HTTP_BAD_REQUEST;

        if ('CLIENT_NOT_FOUND' === $message) {
            $statusCode = Response::HTTP_NOT_FOUND;
        }

        return new JsonResponse([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }
}