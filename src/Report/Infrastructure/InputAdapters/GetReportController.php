<?php

namespace App\Report\Infrastructure\InputAdapters;

use App\Report\Application\DTO\GetReportRequest;
use App\Report\Application\InputPorts\GetReportUseCaseInterface;
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
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GetReportController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly GetReportUseCaseInterface $getReportUseCase,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly LoggerInterface $logger,
        PermissionService $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/report/{id}', name: 'api_report_get', methods: ['GET'])]
    #[OA\Get(
        path: '/api/report/{id}',
        summary: 'Obtiene un informe específico por su ID',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                description: 'ID del informe'
            ),
            new OA\Parameter(
                name: 'uuidClient',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid'),
                description: 'UUID del cliente'
            ),
        ],
        tags: ['Report'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Informe recuperado correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'REPORT_RETRIEVED'),
                        new OA\Property(property: 'report', type: 'object', properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'Informe Semanal Stock'),
                            new OA\Property(property: 'period', type: 'string', example: 'weekly'),
                            new OA\Property(property: 'send_time', type: 'string', example: '08:00:00'),
                            new OA\Property(property: 'report_type', type: 'string', example: 'stock_summary'),
                            new OA\Property(property: 'product_filter', type: 'string', nullable: true, example: 'category:electronics'),
                            new OA\Property(property: 'email', type: 'string', example: 'user@example.com'),
                        ]),
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Datos inválidos'),
            new OA\Response(response: 401, description: 'Usuario no autenticado'),
            new OA\Response(response: 403, description: 'Permisos insuficientes'),
            new OA\Response(response: 404, description: 'Cliente o informe no encontrado'),
        ]
    )]
    public function __invoke(Request $request, int $id): JsonResponse
    {
        try {
            $permissionCheck = $this->checkPermissionJson('report.view', 'No tienes permisos para consultar los informes');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            $payload = $request->query->all();
            if (empty($payload) && '' !== $request->getContent()) {
                $decoded = json_decode($request->getContent(), true);
                if (is_array($decoded)) {
                    $payload = $decoded;
                }
            }

            if (empty($payload['uuidClient']) || !is_string($payload['uuidClient'])) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'REQUIRED_CLIENT_ID',
                ], Response::HTTP_BAD_REQUEST);
            }

            /** @var GetReportRequest $dto */
            $dto = $this->serializer->deserialize(
                json_encode(['uuidClient' => $payload['uuidClient'], 'reportId' => $id], JSON_THROW_ON_ERROR),
                GetReportRequest::class,
                'json'
            );

            $errors = $this->validator->validate($dto);
            if (count($errors) > 0) {
                return $this->validationErrorResponse($errors);
            }

            $user = $this->getUser();
            if (!is_object($user) || !method_exists($user, 'getUuid')) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'USER_NOT_AUTHENTICATED',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $response = $this->getReportUseCase->execute($dto);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'REPORT_RETRIEVED',
                'report' => $response->getReport(),
            ], Response::HTTP_OK);
        } catch (\RuntimeException $exception) {
            return $this->handleRuntimeException($exception);
        } catch (\Throwable $throwable) {
            $this->logger->error('Unexpected error fetching report', [
                'exception' => $throwable->getMessage(),
            ]);

            return new JsonResponse([
                'status' => 'error',
                'message' => 'UNEXPECTED_ERROR',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function validationErrorResponse(ConstraintViolationListInterface $errors): JsonResponse
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

        if ('CLIENT_NOT_FOUND' === $message || 'REPORT_NOT_FOUND' === $message) {
            $statusCode = Response::HTTP_NOT_FOUND;
        }

        return new JsonResponse([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }
}
