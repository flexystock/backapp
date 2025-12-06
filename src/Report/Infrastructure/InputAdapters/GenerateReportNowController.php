<?php

namespace App\Report\Infrastructure\InputAdapters;

use App\Report\Application\DTO\GenerateReportNowRequest;
use App\Report\Application\InputPorts\GenerateReportNowUseCaseInterface;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GenerateReportNowController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly GenerateReportNowUseCaseInterface $generateReportNowUseCase,
        private readonly ValidatorInterface $validator,
        private readonly LoggerInterface $logger,
        PermissionService $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/report/generate-now', name: 'api_report_generate_now', methods: ['POST'])]
    #[OA\Post(
        path: '/api/report/generate-now',
        summary: 'Genera un informe del estado actual del stock y lo envía por email',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'name', 'reportType', 'productFilter', 'email'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid', example: 'c014a415-4113-49e5-80cb-cc3158c15236'),
                    new OA\Property(property: 'name', type: 'string', example: 'Informe Estado Actual Stock'),
                    new OA\Property(property: 'reportType', type: 'string', enum: ['csv', 'pdf'], example: 'csv'),
                    new OA\Property(property: 'productFilter', type: 'string', enum: ['all', 'below_stock', 'specific'], example: 'all'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                    new OA\Property(property: 'period', type: 'string', enum: ['daily', 'weekly', 'monthly'], example: 'daily'),
                    new OA\Property(property: 'productIds', type: 'array', items: new OA\Items(type: 'integer'), example: [1, 2, 3], description: 'IDs de productos (requerido si productFilter=specific)'),
                ]
            )
        ),
        tags: ['Report'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Informe generado y enviado correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'REPORT_GENERATED_AND_SENT'),
                        new OA\Property(property: 'data', type: 'object', properties: [
                            new OA\Property(property: 'report_name', type: 'string', example: 'Informe Estado Actual Stock'),
                            new OA\Property(property: 'report_type', type: 'string', example: 'csv'),
                            new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                            new OA\Property(property: 'products_count', type: 'integer', example: 15),
                        ]),
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
            $permissionCheck = $this->checkPermissionJson('report.create', 'No tienes permisos para generar informes');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            // Leer JSON manualmente para tener control total sobre la construcción del DTO
            $data = json_decode($request->getContent(), true);

            if (!is_array($data)) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'INVALID_JSON',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Construir el DTO manualmente con named parameters
            $generateRequest = new GenerateReportNowRequest(
                uuidClient: $data['uuidClient'] ?? '',
                name: $data['name'] ?? '',
                reportType: $data['reportType'] ?? '',
                productFilter: $data['productFilter'] ?? '',
                email: $data['email'] ?? '',
                period: $data['period'] ?? 'daily',
                productIds: $data['productIds'] ?? []
            );

            // Validar el DTO
            $errors = $this->validator->validate($generateRequest);
            if (count($errors) > 0) {
                return $this->validationErrorResponse($errors);
            }

            // Validación manual: si productFilter es 'specific', productIds no puede estar vacío
            if ($generateRequest->getProductFilter() === 'specific' && empty($generateRequest->getProductIds())) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'PRODUCTS_REQUIRED_FOR_SPECIFIC_FILTER',
                    'errors' => [
                        'productIds' => 'Debe seleccionar al menos un producto cuando el filtro es "specific"',
                    ],
                ], Response::HTTP_BAD_REQUEST);
            }

            $user = $this->getUser();
            if (!is_object($user) || !method_exists($user, 'getUuid')) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'USER_NOT_AUTHENTICATED',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $generateRequest->setUuidUser($user->getUuid());
            $generateRequest->setTimestamp(new \DateTimeImmutable());

            $response = $this->generateReportNowUseCase->execute($generateRequest);

            return new JsonResponse([
                'status' => $response->isSuccess() ? 'success' : 'error',
                'message' => $response->getMessage(),
                'data' => $response->getData(),
            ], Response::HTTP_OK);
        } catch (\RuntimeException $exception) {
            return $this->handleRuntimeException($exception);
        } catch (\Throwable $throwable) {
            $this->logger->error('Unexpected error generating report', [
                'exception' => $throwable->getMessage(),
                'trace' => $throwable->getTraceAsString(),
            ]);

            return new JsonResponse([
                'status' => 'error',
                'message' => 'UNEXPECTED_ERROR',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function validationErrorResponse($errors): JsonResponse
    {
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

    private function handleRuntimeException(\RuntimeException $exception): JsonResponse
    {
        $message = $exception->getMessage();
        $statusCode = Response::HTTP_BAD_REQUEST;

        if ('CLIENT_NOT_FOUND' === $message) {
            $statusCode = Response::HTTP_NOT_FOUND;
        } elseif (str_starts_with($message, 'PRODUCT_NOT_FOUND')) {
            $statusCode = Response::HTTP_NOT_FOUND;
        } elseif ('PRODUCTS_REQUIRED_FOR_SPECIFIC_FILTER' === $message) {
            $statusCode = Response::HTTP_BAD_REQUEST;
        }

        return new JsonResponse([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }
}