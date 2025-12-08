<?php

namespace App\IA\Infrastructure\InputAdapters;

use App\IA\Application\DTO\CreatePredictionConsumeProductRequest;
use App\IA\Application\InputPorts\CreatePredictionConsumeProductUseCaseInterface;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreatePredictionConsumeProductController extends AbstractController
{
    use PermissionControllerTrait;

    private CreatePredictionConsumeProductUseCaseInterface $useCase;
    private LoggerInterface $logger;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        LoggerInterface $logger,
        CreatePredictionConsumeProductUseCaseInterface $useCase,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        PermissionService $permissionService
    ) {
        $this->logger = $logger;
        $this->useCase = $useCase;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->permissionService = $permissionService;
    }

    #[Route('/api/ia/prediction/product', name: 'api_ia_prediction_product', methods: ['POST'])]
    #[OA\Post(
        path: '/api/ia/prediction/product',
        summary: 'Crear predicción de consumo para un producto específico',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'productId'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid', example: 'c014a415-4113-49e5-80cb-cc3158c15236'),
                    new OA\Property(property: 'productId', type: 'integer', example: 1),
                ],
                type: 'object'
            )
        ),
        tags: ['IA - Predictions'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Predicción creada con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'Prediction created successfully'),
                        new OA\Property(
                            property: 'prediction',
                            properties: [
                                new OA\Property(property: 'product_id', type: 'integer', example: 1),
                                new OA\Property(property: 'product_name', type: 'string', example: 'Producto 1'),
                                new OA\Property(property: 'current_weight', type: 'number', example: 15.5),
                                new OA\Property(property: 'min_stock', type: 'number', example: 5.0),
                                new OA\Property(property: 'consumption_rate', type: 'number', example: 0.5),
                                new OA\Property(property: 'days_until_min_stock', type: 'number', example: 21.0),
                                new OA\Property(property: 'stock_depletion_date', type: 'string', example: '2024-01-15 10:00:00'),
                                new OA\Property(property: 'recommended_restock_date', type: 'string', example: '2024-01-13 10:00:00'),
                                new OA\Property(property: 'days_serve_order', type: 'integer', example: 2),
                            ],
                            type: 'object',
                        ),
                    ],
                    type: 'object',
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Datos insuficientes para generar predicción',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'INSUFFICIENT_DATA'),
                    ],
                    type: 'object',
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Cliente o producto no encontrado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'error'),
                        new OA\Property(property: 'message', type: 'string', example: 'PRODUCT_NOT_FOUND'),
                    ],
                    type: 'object',
                )
            ),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            // Check permissions
            $permissionCheck = $this->checkPermissionJson('product.view', 'No tienes permisos para ver predicciones de productos');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            // Deserialize request
            $predictionRequest = $this->serializer->deserialize(
                $request->getContent(),
                CreatePredictionConsumeProductRequest::class,
                'json'
            );

            // Validate request
            $errors = $this->validator->validate($predictionRequest);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                }

                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'INVALID_DATA',
                    'errors' => $errorMessages,
                ], Response::HTTP_BAD_REQUEST);
            }

            // Execute use case
            $response = $this->useCase->execute($predictionRequest);

            if ($response->getError()) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => $response->getError(),
                ], $response->getStatusCode());
            }

            return new JsonResponse([
                'status' => 'success',
                'message' => 'Prediction created successfully',
                'prediction' => $response->getPrediction(),
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Error in CreatePredictionConsumeProductController: '.$e->getMessage());

            return new JsonResponse([
                'status' => 'error',
                'message' => 'INTERNAL_SERVER_ERROR',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
