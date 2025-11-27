<?php

namespace App\Report\Infrastructure\InputAdapters;

use App\Report\Application\DTO\CreateReportRequest;
use App\Report\Application\InputPorts\CreateReportUseCaseInterface;
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

class CreateReportController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly CreateReportUseCaseInterface $createReportUseCase,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly LoggerInterface $logger,
        PermissionService $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/report', name: 'api_report_create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/report',
        summary: 'Crea un nuevo informe para un cliente',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'name', 'period', 'sendTime', 'reportType', 'email'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid', example: 'c014a415-4113-49e5-80cb-cc3158c15236'),
                    new OA\Property(property: 'name', type: 'string', example: 'Informe Semanal Stock'),
                    new OA\Property(property: 'period', type: 'string', example: 'weekly'),
                    new OA\Property(property: 'sendTime', type: 'string', format: 'time', example: '08:00:00'),
                    new OA\Property(property: 'reportType', type: 'string', example: 'stock_summary'),
                    new OA\Property(property: 'productFilter', type: 'string', nullable: true, example: 'category:electronics'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                ]
            )
        ),
        tags: ['Report'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Informe creado correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'REPORT_CREATED'),
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
            new OA\Response(response: 404, description: 'Cliente no encontrado'),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $permissionCheck = $this->checkPermissionJson('report.create', 'No tienes permisos para crear informes');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            /** @var CreateReportRequest $createRequest */
            $createRequest = $this->serializer->deserialize(
                $request->getContent(),
                CreateReportRequest::class,
                'json'
            );

            $errors = $this->validator->validate($createRequest);
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

            $createRequest->setUuidUser($user->getUuid());
            $createRequest->setTimestamp(new \DateTimeImmutable());

            $response = $this->createReportUseCase->execute($createRequest);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'REPORT_CREATED',
                'report' => $response->getReport(),
            ], Response::HTTP_CREATED);
        } catch (\RuntimeException $exception) {
            return $this->handleRuntimeException($exception);
        } catch (\Throwable $throwable) {
            $this->logger->error('Unexpected error creating report', [
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

        if ('CLIENT_NOT_FOUND' === $message) {
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
