<?php

namespace App\Product\Infrastructure\InputAdapters;

use App\Product\Application\DTO\GetAllProductsRequest;
use App\Product\Application\DTO\GetProductRequest;
use App\Product\Application\InputPorts\GetAllProductsUseCaseInterface;
use App\Product\Application\InputPorts\GetProductUseCaseInterface;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetProductController extends AbstractController
{
    use PermissionControllerTrait;

    private GetProductUseCaseInterface $getProductUseCase;
    private GetAllProductsUseCaseInterface $getAllProductsUseCase;
    private LoggerInterface $logger;

    public function __construct(
        GetProductUseCaseInterface $getProductUseCase, 
        LoggerInterface $logger,
        GetAllProductsUseCaseInterface $getAllProductsUseCase,
        PermissionService $permissionService
    ) {
        $this->getProductUseCase = $getProductUseCase;
        $this->getAllProductsUseCase = $getAllProductsUseCase;
        $this->logger = $logger;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/product', name: 'api_product', methods: ['POST'])]
    #[RequiresPermission('product.view')]
    #[OA\Post(
        path: '/api/product',
        summary: 'Obtener información de un producto para un cliente',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'uuid'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid', example: 'c014a415-4113-49e5-80cb-cc3158c15236'),
                    new OA\Property(property: 'uuid', type: 'string', format: 'uuid', example: '9a6ae1c0-3bc6-41c8-975a-4de5b4357666'),
                ],
                type: 'object'
            )
        ),
        tags: ['Product'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Información del producto devuelta con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'product',
                            properties: [
                                new OA\Property(property: 'uuid', type: 'string', format: 'uuid', example: '9a6ae1c0-3bc6-41c8-975a-4de5b4357666'),
                                new OA\Property(property: 'name', type: 'string', example: 'producto1'),
                            ],
                            type: 'object',
                        ),
                    ],
                    type: 'object',
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Faltan "uuidClient" o "uuid", o el formato es inválido',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'uuidClient and uuid are required'),
                    ],
                    type: 'object',
                )
            ),
            new OA\Response(
                response: 401,
                description: 'No autenticado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Unauthorized'),
                    ],
                    type: 'object',
                )
            ),
            new OA\Response(
                response: 403,
                description: 'El usuario no tiene acceso al cliente especificado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Access denied to the specified client'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Error interno del servidor',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Internal Server Error'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function getProductByUuid(Request $request): JsonResponse
    {
        $permissionCheck = $this->checkPermissionJson('product.view');
        if ($permissionCheck) {
            return $permissionCheck;
        }

        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;
        $uuidProduct = $data['uuid'] ?? null;

        if (!$uuidClient || !$uuidProduct) {
            $this->logger->warning('ProductController: uuid_client o uuidProduct no proporcionado.');

            return new JsonResponse(['error' => 'uuidClient and uuid are required'], 400);
        }

        // Validar formato de UUID
        if (!$this->isValidUuid($uuidClient) || !$this->isValidUuid($uuidProduct)) {
            $this->logger->warning('ProductController: uuidClient o uuidProduct con formato inválido.');

            return new JsonResponse(['error' => 'Invalid uuid format'], 400);
        }

        // Verificar que el usuario tiene acceso al cliente
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        if (!$user->getClients()->exists(function ($key, $client) use ($uuidClient) {
            return $client->getUuidClient() === $uuidClient;
        })) {
            $this->logger->warning('ProductController: Usuario no tiene acceso al cliente proporcionado.');

            return new JsonResponse(['error' => 'Access denied to the specified client'], 403);
        }

        try {
            // Crear el DTO de la solicitud
            $getProductRequest = new GetProductRequest($uuidClient, $uuidProduct);

            // Ejecutar el caso de uso
            $response = $this->getProductUseCase->execute($getProductRequest);

            if ($response->getError()) {
                return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
            }

            return new JsonResponse(['product' => $response->getProduct()], $response->getStatusCode());
        } catch (\Exception $e) {
            $this->logger->error('ProductController: Error al obtener el producto.', ['exception' => $e]);

            return new JsonResponse(['error' => 'Internal Server Error'], 500);
        }
    }

    #[Route('/api/product_all', name: 'api_product_all', methods: ['POST'])]
    #[RequiresPermission('product.view')]
    #[OA\Post(
        path: '/api/product_all',
        summary: 'Obtener información de todos los productos de un cliente',
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
        tags: ['Product'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Información de los productos devueltos con exito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'product',
                            properties: [
                                new OA\Property(property: 'uuid', type: 'string', format: 'uuid', example: '9a6ae1c0-3bc6-41c8-975a-4de5b4357666'),
                                new OA\Property(property: 'name', type: 'string', example: 'producto1'),
                            ],
                            type: 'object',
                        ),
                    ],
                    type: 'object',
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Faltan "uuidClient" o el formato es inválido',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'uuidClient are required'),
                    ],
                    type: 'object',
                )
            ),
            new OA\Response(
                response: 401,
                description: 'No autenticado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Unauthorized'),
                    ],
                    type: 'object',
                )
            ),
            new OA\Response(
                response: 403,
                description: 'El usuario no tiene acceso al cliente especificado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Access denied to the specified client'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Error interno del servidor',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Internal Server Error'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function getAllProducts(Request $request): JsonResponse
    {
        $permissionCheck = $this->checkPermissionJson('product.view');
        if ($permissionCheck) {
            return $permissionCheck;
        }

        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;

        if (!$uuidClient) {
            $this->logger->warning('ProductController: uuidClient no proporcionado.');

            return new JsonResponse(['error' => 'uuidClient are required'], 400);
        }
        // Verificar que el usuario tiene acceso al cliente
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['message' => 'USER_NOT_AUTHENTICATED'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user->getClients()->exists(function ($key, $client) use ($uuidClient) {
            return $client->getUuidClient() === $uuidClient;
        })) {
            $this->logger->warning('ProductController: Usuario no tiene acceso al cliente proporcionado.');

            return new JsonResponse(['error' => 'Access denied to the specified client'], 403);
        }

        try {
            // Crear el DTO de la solicitud
            $getAllProductRequest = new GetAllProductsRequest($uuidClient);

            // Ejecutar el caso de uso
            $response = $this->getAllProductsUseCase->execute($getAllProductRequest);

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
        return 1 === preg_match('/^[0-9a-fA-F-]{36}$/', $uuid);
    }
}
