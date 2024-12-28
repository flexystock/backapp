<?php

// src/Product/Infrastructure/InputAdapters/ProductController.php

namespace App\Product\Infrastructure\InputAdapters;

use App\Product\Application\DTO\CreateProductRequest;
use App\Product\Application\DTO\DeleteProductRequest;
use App\Product\Application\DTO\GetAllProductsRequest;
use App\Product\Application\DTO\GetProductRequest;
use App\Product\Application\DTO\UpdateProductRequest;
use App\Product\Application\InputPorts\CreateProductUseCaseInterface;
use App\Product\Application\InputPorts\DeleteProductUseCaseInterface;
use App\Product\Application\InputPorts\GetAllProductsUseCaseInterface;
use App\Product\Application\InputPorts\GetProductUseCaseInterface;
use App\Product\Application\InputPorts\UpdateProductUseCaseInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private GetProductUseCaseInterface $getProductUseCase;
    private GetAllProductsUseCaseInterface $getAllProductsUseCase;
    private CreateProductUseCaseInterface $createProductUseCase;
    private LoggerInterface $logger;
    private DeleteProductUseCaseInterface $deleteProductUseCase;
    private UpdateProductUseCaseInterface $updateProductUseCase;

    public function __construct(GetProductUseCaseInterface $getProductUseCase, LoggerInterface $logger,
        GetAllProductsUseCaseInterface $getAllProductsUseCase, CreateProductUseCaseInterface $createProductUseCase,
        DeleteProductUseCaseInterface $deleteProductUseCase, UpdateProductUseCaseInterface $updateProductUseCase)
    {
        $this->getProductUseCase = $getProductUseCase;
        $this->getAllProductsUseCase = $getAllProductsUseCase;
        $this->logger = $logger;
        $this->createProductUseCase = $createProductUseCase;
        $this->deleteProductUseCase = $deleteProductUseCase;
        $this->updateProductUseCase = $updateProductUseCase;
    }

    #[Route('/api/product', name: 'api_product', methods: ['POST'])]
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
        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;

        if (!$uuidClient) {
            $this->logger->warning('ProductController: uuidClient no proporcionado.');

            return new JsonResponse(['error' => 'uuidClient are required'], 400);
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

    #[Route('/api/product_create', name: 'api_product_create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/product_create',
        summary: 'Crear un producto para un cliente',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'name'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid', example: 'c014a415-4113-49e5-80cb-cc3158c15236'),
                    new OA\Property(property: 'name', type: 'string', example: 'Nuevo producto'),
                    new OA\Property(property: 'ean', type: 'string', example: '1234567890123', nullable: true),
                    new OA\Property(property: 'weightRange', type: 'number', format: 'float', example: 0.2, nullable: true),
                    new OA\Property(property: 'nameUnit1', type: 'string', example: 'pack', nullable: true),
                    new OA\Property(property: 'weightUnit1', type: 'number', format: 'float', example: 0.5, nullable: true),
                    new OA\Property(property: 'nameUnit2', type: 'string', example: 'litros', nullable: true),
                    new OA\Property(property: 'weightUnit2', type: 'number', format: 'float', example: 2.0, nullable: true),
                    new OA\Property(property: 'mainUnit', type: 'string', enum: ['0', '1', '2'], example: '0'),
                    new OA\Property(property: 'tare', type: 'number', format: 'float', example: 0.0),
                    new OA\Property(property: 'salePrice', type: 'number', format: 'float', example: 2.00),
                    new OA\Property(property: 'costPrice', type: 'number', format: 'float', example: 1.20),
                    new OA\Property(property: 'outSystemStock', type: 'boolean', example: false, nullable: true),
                    new OA\Property(property: 'daysAverageConsumption', type: 'integer', example: 30),
                    new OA\Property(property: 'daysServeOrder', type: 'integer', example: 0),
                    new OA\Property(property: 'uuidUserCreation', type: 'string', format: 'uuid', example: 'adf299d0-d420-4c84-8213-33411353287f', nullable: true),
                    new OA\Property(property: 'datehourCreation', type: 'string', format: 'date-time', example: '2024-12-16T10:00:00Z', nullable: true),
                ],
                type: 'object'
            )
        ),
        tags: ['Product'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Producto creado con éxito',
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
                description: 'Faltan campos obligatorios o formato inválido',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'uuid_client, name or description are required'),
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
    public function createProduct(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $uuidClient = $data['uuidClient'] ?? null;
        $name = $data['name'] ?? null;

        if (!$uuidClient || !$name) {
            $this->logger->warning('ProductController: uuidClient or name are required.');

            return new JsonResponse(['error' => 'uuidClient, name or description are required'], 400);
        }
        // Verificar que el usuario tiene acceso al cliente
        $user = $this->getUser();
        $uuidUserCreation = $user->getUuid();
        $datehourCreation = new \DateTime();

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
            $createProductRequest = new CreateProductRequest($uuidClient,
                $name,
                $uuidUserCreation,
                $datehourCreation,
                30,
                0,
                '0',
                0.0,
                0.00,
                0.00,
                null,
                null,
                null,
                null,
                null,
                null,
                null);

            // Ejecutar el caso de uso
            $response = $this->createProductUseCase->execute($createProductRequest);

            if ($response->getError()) {
                return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
            }

            return new JsonResponse(['product' => $response->getProduct()], $response->getStatusCode());
        } catch (\Exception $e) {
            $this->logger->error('ProductController: Error al crear el producto.', ['exception' => $e]);

            return new JsonResponse(['error' => 'Internal Server Error'], 500);
        }
    }

    #[Route('/api/product_delete', name: 'api_product_delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/product_delete',
        summary: 'Eliminar un producto para un cliente',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuid_client', 'uuidProduct'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid', example: 'c014a415-4113-49e5-80cb-cc3158c15236'),
                    new OA\Property(property: 'uuidProduct', type: 'string', format: 'uuid', example: '9a6ae1c0-3bc6-41c8-975a-4de5b4357666'),
                ],
                type: 'object'
            )
        ),
        tags: ['Product'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Producto eliminado con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Product deleted successfully'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Faltan campos uuidClient o uuid_product',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Missing required fields: uuidClient or uuidProduct'),
                    ],
                    type: 'object'
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
                response: 404,
                description: 'Producto no encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Product not found'),
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
    public function deleteProduct(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;
        $uuidProduct = $data['uuidProduct'] ?? null;

        if (!$uuidClient || !$uuidProduct) {
            $this->logger->warning('ProductController: uuidClient o uuidProduct no proporcionado.');

            return new JsonResponse(['error' => 'Missing required fields: uuidClient or uuidProduct'], 400);
        }

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
            $deleteProductRequest = new DeleteProductRequest($uuidClient, $uuidProduct);
            $response = $this->deleteProductUseCase->execute($deleteProductRequest);

            if ($response->getError()) {
                return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
            }

            return new JsonResponse(['message' => $response->getMessage()], $response->getStatusCode());
        } catch (\Exception $e) {
            $this->logger->error('ProductController: Error al eliminar el producto.', ['exception' => $e]);

            return new JsonResponse(['error' => 'Internal Server Error'], 500);
        }
    }

    #[Route('/api/product_update', name: 'api_product_update', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/product_update',
        summary: 'Actualizar un producto para un cliente',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'uuidProduct'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid', example: 'c014a415-4113-49e5-80cb-cc3158c15236'),
                    new OA\Property(property: 'uuidProduct', type: 'string', format: 'uuid', example: '9a6ae1c0-3bc6-41c8-975a-4de5b4357666'),
                    new OA\Property(property: 'name', type: 'string', example: 'Nuevo nombre del producto'),
                    new OA\Property(property: 'ean', type: 'string', example: '1234567890123', nullable: true),
                    new OA\Property(property: 'weightRange', type: 'number', format: 'float', example: 0.2, nullable: true),
                    new OA\Property(property: 'nameUnit1', type: 'string', example: 'pack', nullable: true),
                    new OA\Property(property: 'weightUnit1', type: 'number', format: 'float', example: 0.5, nullable: true),
                    new OA\Property(property: 'nameUnit2', type: 'string', example: 'litros', nullable: true),
                    new OA\Property(property: 'weightUnit2', type: 'number', format: 'float', example: 2.0, nullable: true),
                    new OA\Property(property: 'mainUnit', type: 'string', enum: ['0', '1', '2'], example: '0'),
                    new OA\Property(property: 'tare', type: 'number', format: 'float', example: 0.0),
                    new OA\Property(property: 'salePrice', type: 'number', format: 'float', example: 2.00),
                    new OA\Property(property: 'costPrice', type: 'number', format: 'float', example: 1.20),
                    new OA\Property(property: 'outSystemStock', type: 'boolean', example: false, nullable: true),
                    new OA\Property(property: 'daysAverageConsumption', type: 'integer', example: 30),
                    new OA\Property(property: 'daysServeOrder', type: 'integer', example: 0),
                ],
                type: 'object'
            )
        ),
        tags: ['Product'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Producto actualizado con éxito',
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
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Faltan campos uuid_client o uuid_product',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Missing required fields: uuid_client or uuid_product'),
                    ],
                    type: 'object'
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
                response: 404,
                description: 'Producto no encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Product not found'),
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
    public function updateProduct(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;
        $uuidProduct = $data['uuidProduct'] ?? null;

        if (!$uuidClient || !$uuidProduct) {
            $this->logger->warning('ProductController: uuidClient o uuidProduct no proporcionado.');

            return new JsonResponse(['error' => 'Missing required fields: uuidClient or uuidProduct'], 400);
        }

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

        $uuidUserModification = $user->getUuid();
        $datehourModification = new \DateTime();

        try {
            $updateRequest = new UpdateProductRequest(
                $uuidClient,
                $uuidProduct,
                $uuidUserModification,
                $datehourModification,
                $data['name'] ?? null,
                $data['ean'] ?? null,
                isset($data['weightRange']) && '' !== $data['weightRange'] ? (float) $data['weightRange'] : null,
                $data['nameUnit1'] ?? null,
                isset($data['weightUnit1']) && '' !== $data['weightUnit1'] ? (float) $data['weightUnit1'] : null,
                $data['nameUnit2'] ?? null,
                isset($data['weightUnit2']) && '' !== $data['weightUnit2'] ? (float) $data['weightUnit2'] : null,
                $data['mainUnit'] ?? null,
                isset($data['tare']) && '' !== $data['tare'] ? (float) $data['tare'] : null,
                isset($data['salePrice']) && '' !== $data['salePrice'] ? (float) $data['salePrice'] : null,
                isset($data['costPrice']) && '' !== $data['costPrice'] ? (float) $data['costPrice'] : null,
                isset($data['outSystemStock']) && '' !== $data['outSystemStock'] ? (bool) $data['outSystemStock'] : null,
                isset($data['daysAverageConsumption']) && '' !== $data['daysAverageConsumption'] ? (int) $data['daysAverageConsumption'] : null,
                isset($data['daysServeOrder']) && '' !== $data['daysServeOrder'] ? (int) $data['daysServeOrder'] : null,
            );

            $response = $this->updateProductUseCase->execute($updateRequest);

            if ($response->getError()) {
                return new JsonResponse(['error' => $response->getError()], $response->getStatusCode());
            }

            return new JsonResponse(['product' => $response->getProduct()], $response->getStatusCode());
        } catch (\Exception $e) {
            $this->logger->error('ProductController: Error al actualizar el producto.', ['exception' => $e]);

            return new JsonResponse(['error' => 'Internal Server Error'], 500);
        }
    }
}
