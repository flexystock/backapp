<?php

namespace App\Product\Infrastructure\InputAdapters;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Product\Application\DTO\UpdateProductRequest;
use App\Product\Application\InputPorts\UpdateProductUseCaseInterface;
use App\Security\ClientAccessControlTrait;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateProductController extends AbstractController
{
    use PermissionControllerTrait;
    use ClientAccessControlTrait;

    private UpdateProductUseCaseInterface $updateProductUseCase;
    private LoggerInterface $logger;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private ClientRepositoryInterface $clientRepository;

    public function __construct(
        LoggerInterface $logger,
        UpdateProductUseCaseInterface $updateProductUseCase,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PermissionService $permissionService,
        ClientRepositoryInterface $clientRepository
    ) {
        $this->logger = $logger;
        $this->updateProductUseCase = $updateProductUseCase;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->permissionService = $permissionService;
        $this->clientRepository = $clientRepository;
    }

    #[Route('/api/product_update', name: 'api_product_update', methods: ['PUT'])]
    #[RequiresPermission('product.update')]
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
    public function invoke(Request $request): JsonResponse
    {
        $permissionCheck = $this->checkPermissionJson('product.update');
        if ($permissionCheck) {
            return $permissionCheck;
        }

        // Verify client access - must have active subscription
        $requestData = json_decode($request->getContent(), true);
        if (!isset($requestData['uuidClient'])) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Missing required field: uuidClient',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $client = $this->clientRepository->findByUuid($requestData['uuidClient']);
        if (!$client) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'CLIENT_NOT_FOUND',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $clientAccessCheck = $this->verifyClientAccess($client);
        if ($clientAccessCheck) {
            return $clientAccessCheck; // Returns 402 Payment Required
        }

        try {
            // 1) Deserializar el JSON al DTO UpdateProductRequest
            $updateRequest = $this->serializer->deserialize(
                $request->getContent(),
                UpdateProductRequest::class,
                'json'
            );

            // 2) Validar el DTO con Symfony Validator
            $errors = $this->validator->validate($updateRequest);
            if (count($errors) > 0) {
                $errorMessages = $this->formatValidationErrors($errors);

                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'INVALID_DATA',
                    'errors' => $errorMessages,
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            // 3) Asignar manualmente (porque el user no llega en el JSON)
            $user = $this->getUser();
            if (!$user) {
                return $this->jsonError('USER_NOT_AUTHENTICATED', JsonResponse::HTTP_UNAUTHORIZED);
            }

            // 4) Asignar el userModification
            $updateRequest->setUuidUserModification($user->getUuid());

            // 5) Asignar fecha de modificación
            $updateRequest->setDatehourModification(new \DateTime());

            // 6) Ejecutar el caso de uso
            $response = $this->updateProductUseCase->execute($updateRequest);

            // 7) Respuesta exitosa
            return new JsonResponse([
                'status' => 'success',
                'message' => 'PRODUCT_UPDATED_SUCCESSFULLY',
                'product' => $response->getProduct(), // Por ej. array con uuid, name...
            ], $response->getStatusCode());
        } catch (\RuntimeException $e) {
            // Errores de dominio esperados (p.ej. "PRODUCT_NOT_FOUND", "CLIENT_NOT_FOUND", etc.)
            if ('PRODUCT_NOT_FOUND' === $e->getMessage()) {
                return $this->jsonError('PRODUCT_NOT_FOUND', JsonResponse::HTTP_NOT_FOUND);
            }
            if ('CLIENT_NOT_FOUND' === $e->getMessage()) {
                return $this->jsonError('CLIENT_NOT_FOUND', JsonResponse::HTTP_NOT_FOUND);
            }
            if ('USER_NOT_AUTHENTICATED' === $e->getMessage()) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'USER_NOT_AUTHENTICATED',
                ], Response::HTTP_UNAUTHORIZED);
            }
            // etc. Manejo 403, 409, etc.

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
