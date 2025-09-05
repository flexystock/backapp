<?php

namespace App\Product\Infrastructure\InputAdapters;

use App\Product\Application\DTO\DeleteProductRequest;
use App\Product\Application\InputPorts\DeleteProductUseCaseInterface;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DeleteProductController extends AbstractController
{
    use PermissionControllerTrait;

    private DeleteProductUseCaseInterface $deleteProductUseCase;
    private LoggerInterface $logger;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        LoggerInterface $logger, 
        DeleteProductUseCaseInterface $deleteProductUseCase,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PermissionService $permissionService
    ) {
        $this->logger = $logger;
        $this->deleteProductUseCase = $deleteProductUseCase;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/product_delete', name: 'api_product_delete', methods: ['DELETE'])]
    #[RequiresPermission('product.delete')]
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
    public function __invoke(Request $request): JsonResponse
    {
        $permissionCheck = $this->checkPermissionJson('product.delete');
        if ($permissionCheck) {
            return $permissionCheck;
        }

        try {
            // 1) Deserializar JSON => DTO
            $deleteProductRequest = $this->serializer->deserialize(
                $request->getContent(),
                DeleteProductRequest::class,
                'json'
            );

            // 2) Validar el DTO con Symfony Validator
            $errors = $this->validator->validate($deleteProductRequest);
            if (count($errors) > 0) {
                $errorMessages = $this->formatValidationErrors($errors);

                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'INVALID_DATA',
                    'errors' => $errorMessages,
                ], Response::HTTP_BAD_REQUEST);
            }

            // 3) COMPROBAR SI EL USUARIO HA LOGGEADO
            $user = $this->getUser();
            if (!$user) {
                return new JsonResponse(['message' => 'USER_NOT_AUTHENTICATED'], Response::HTTP_UNAUTHORIZED);
            }

            // 4) Ejecutar el caso de uso
            $response = $this->deleteProductUseCase->execute($deleteProductRequest, $user);

            // 5) Devolver la respuesta en caso de éxito
            return new JsonResponse(['message' => $response->getMessage()], $response->getStatusCode());
        } catch (\Exception $e) {
            // Manejo de excepciones esperadas
            if ('PRODUCT_NOT_FOUND' === $e->getMessage()) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'PRODUCT_NOT_FOUND',
                ], Response::HTTP_NOT_FOUND);
            }
            // Manejo de excepciones esperadas
            if ('CLIENT_NOT_FOUND' === $e->getMessage()) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'CLIENT_NOT_FOUND',
                ], Response::HTTP_NOT_FOUND);
            }
            // Manejo de excepciones esperadas
            if ('ACCESS_DENIED' === $e->getMessage()) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'ACCESS_DENIED',
                ], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            // Manejo de errores inesperados
            $this->logger->error('Error inesperado al eliminar el producto', ['exception' => $e]);

            return new JsonResponse([
                'status' => 'error',
                'message' => 'Internal Server Error',
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
