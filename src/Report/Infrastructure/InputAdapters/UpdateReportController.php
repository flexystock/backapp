<?php

namespace App\Report\Infrastructure\InputAdapters;

use App\Report\Application\DTO\UpdateReportRequest;
use App\Report\Application\InputPorts\UpdateReportUseCaseInterface;
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

class UpdateReportController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly UpdateReportUseCaseInterface $updateReportUseCase,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly LoggerInterface $logger,
        PermissionService $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/report/{id}', name: 'api_report_update', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/report/{id}',
        summary: 'Actualiza un informe existente',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer'),
                description: 'ID del informe a actualizar'
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'name', 'period', 'sendTime', 'reportType', 'email'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid', example: 'c014a415-4113-49e5-80cb-cc3158c15236'),
                    new OA\Property(property: 'name', type: 'string', example: 'Informe Semanal Stock Actualizado'),
                    new OA\Property(property: 'period', type: 'string', example: 'daily'),
                    new OA\Property(property: 'sendTime', type: 'string', format: 'time', example: '09:00:00'),
                    new OA\Property(property: 'reportType', type: 'string', example: 'stock_detail'),
                    new OA\Property(property: 'productFilter', type: 'string', nullable: true, example: 'category:all'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'updated@example.com'),
                ]
            )
        ),
        tags: ['Report'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Informe actualizado correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'REPORT_UPDATED'),
                        new OA\Property(property: 'report', type: 'object', properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'Informe Semanal Stock Actualizado'),
                            new OA\Property(property: 'period', type: 'string', example: 'daily'),
                            new OA\Property(property: 'send_time', type: 'string', example: '09:00:00'),
                            new OA\Property(property: 'report_type', type: 'string', example: 'stock_detail'),
                            new OA\Property(property: 'product_filter', type: 'string', nullable: true, example: 'category:all'),
                            new OA\Property(property: 'email', type: 'string', example: 'updated@example.com'),
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
            $permissionCheck = $this->checkPermissionJson('report.update', 'No tienes permisos para actualizar los informes');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            $content = $request->getContent();
            $data = json_decode($content, true);

            if (!is_array($data)) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'INVALID_JSON',
                ], Response::HTTP_BAD_REQUEST);
            }

            $data['reportId'] = $id;

            /** @var UpdateReportRequest $updateRequest */
            $updateRequest = $this->serializer->deserialize(
                json_encode($data, JSON_THROW_ON_ERROR),
                UpdateReportRequest::class,
                'json'
            );

            $errors = $this->validator->validate($updateRequest);
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

            $updateRequest->setUuidUser($user->getUuid());
            $updateRequest->setTimestamp(new \DateTimeImmutable());

            $response = $this->updateReportUseCase->execute($updateRequest);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'REPORT_UPDATED',
                'report' => $response->getReport(),
            ], Response::HTTP_OK);
        } catch (\RuntimeException $exception) {
            return $this->handleRuntimeException($exception);
        } catch (\Throwable $throwable) {
            $this->logger->error('Unexpected error updating report', [
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
        } elseif ('INVALID_SEND_TIME' === $message) {
            $statusCode = Response::HTTP_BAD_REQUEST;
        }

        return new JsonResponse([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }
}
