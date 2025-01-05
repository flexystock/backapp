<?php

namespace App\Product\Infrastructure\InputAdapters;

use App\Product\Application\DTO\DeleteProductRequest;
use App\Product\Application\InputPorts\DeleteProductUseCaseInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DeleteProductController extends AbstractController
{
    private DeleteProductUseCaseInterface $deleteProductUseCase;
    private LoggerInterface $logger;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(LoggerInterface $logger, DeleteProductUseCaseInterface $deleteProductUseCase,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ) {
        $this->logger = $logger;
        $this->deleteProductUseCase = $deleteProductUseCase;
        $this->serializer = $serializer;
        $this->validator = $validator;
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
}
