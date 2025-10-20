<?php

namespace App\Product\Infrastructure\InputAdapters;

use App\Product\Application\DTO\CreateProductRequest;
use App\Product\Application\InputPorts\CreateProductUseCaseInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;

class CreateProductController extends AbstractController
{
    use PermissionControllerTrait;
    private CreateProductUseCaseInterface $createProductUseCase;
    private LoggerInterface $logger;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(LoggerInterface $logger, CreateProductUseCaseInterface $createProductUseCase,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PermissionService $permissionService
    ) {
        $this->logger = $logger;
        $this->createProductUseCase = $createProductUseCase;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->permissionService = $permissionService;
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
                    new OA\Property(property: 'expiration_date', type: 'string', format: 'date-time', example: '2023-01-01T00:00:00+00:00', nullable: true),
                    new OA\Property(property: 'perishable', type: 'boolean', example: true, nullable: true),
                    new OA\Property(property: 'stock', type: 'number', format: 'float', example: 0.2, nullable: true),
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
                    new OA\Property(property: 'minPercentage', type: 'integer', example: 0),
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
    public function __invoke(Request $request): JsonResponse
    {
        try {
            // Modern permission check - replace the old role checks
            $permissionCheck = $this->checkPermissionJson('product.create', 'No tienes permisos para crear un producto');
            if ($permissionCheck) {
                return $permissionCheck;
            }
            // 1) Deserializar JSON => DTO
            $createProductRequest = $this->serializer->deserialize(
                $request->getContent(),
                CreateProductRequest::class,
                'json'
            );

            // 2) Validar el DTO con Symfony Validator
            $errors = $this->validator->validate($createProductRequest);
            if (count($errors) > 0) {
                $errorMessages = $this->formatValidationErrors($errors);

                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'INVALID_DATA',
                    'errors' => $errorMessages,
                ], Response::HTTP_BAD_REQUEST);
            }

            // 3) Asignar manualmente (porque el user no llega en el JSON)
            $user = $this->getUser();
            if (!$user) {
                return new JsonResponse(['message' => 'USER_NOT_AUTHENTICATED'], Response::HTTP_UNAUTHORIZED);
            }

            // 4) Asignar el userCreation
            $createProductRequest->setUuidUserCreation($user->getUuid());

            // 5) Asignar fecha de creación
            $createProductRequest->setDatehourCreation(new \DateTime());

            // 6) Ejecutar el caso de uso
            $product = $this->createProductUseCase->execute($createProductRequest);

            // 7) Devolver la respuesta exitosa con 201
            $productArray = $product->getProduct(); // Esto te da el array => ['uuid' => '...', 'name' => '...']

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Product created successfully',
                'product' => $productArray,
            ], Response::HTTP_CREATED);
        } catch (\RuntimeException $e) {
            // Manejo de excepciones de dominio esperadas
            if ('CLIENT_NOT_FOUND' === $e->getMessage()) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'CLIENT_NOT_FOUND',
                ], Response::HTTP_NOT_FOUND);
            }
            if ('USER_NOT_AUTHENTICATED' === $e->getMessage()) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'USER_NOT_AUTHENTICATED',
                ], Response::HTTP_UNAUTHORIZED);
            }
            // etc. Maneja 403, 409, etc.

            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            // Errores inesperados
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Formatea la lista de errores de validación en un array asociativo.
     */
    private function formatValidationErrors(ConstraintViolationListInterface $errors): array
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $field = $error->getPropertyPath();
            $message = $error->getMessage();
            $errorMessages[$field] = $message;
        }

        return $errorMessages;
    }
}
