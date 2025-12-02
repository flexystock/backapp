<?php

namespace App\Report\Infrastructure\InputAdapters;

use App\Report\Application\DTO\GetInfoToDashBoardRequest;
use App\Report\Application\InputPorts\GetInfoToDashBoardUseCaseInterface;
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

class GetInfoToDashBoardController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly GetInfoToDashBoardUseCaseInterface $getInfoToDashBoardUseCase,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly LoggerInterface $logger,
        PermissionService $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/report/dashboard', name: 'api_report_dashboard', methods: ['GET'])]
    #[OA\Get(
        path: '/api/report/dashboard',
        summary: 'Obtiene información resumida de informes para el dashboard',
        parameters: [
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
                description: 'Información del dashboard recuperada correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'DASHBOARD_INFO_RETRIEVED'),
                        new OA\Property(property: 'dashboard_info', type: 'object', properties: [
                            new OA\Property(property: 'total_reports', type: 'integer', example: 10),
                            new OA\Property(property: 'reports_by_type', type: 'object', example: '{"stock_summary": 5, "stock_detail": 3}'),
                            new OA\Property(property: 'reports_by_period', type: 'object', example: '{"daily": 4, "weekly": 6}'),
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
            $permissionCheck = $this->checkPermissionJson('report.view', 'No tienes permisos para consultar el dashboard de informes');
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

            /** @var GetInfoToDashBoardRequest $dto */
            $dto = $this->serializer->deserialize(
                json_encode(['uuidClient' => $payload['uuidClient']], JSON_THROW_ON_ERROR),
                GetInfoToDashBoardRequest::class,
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

            $response = $this->getInfoToDashBoardUseCase->execute($dto);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'DASHBOARD_INFO_RETRIEVED',
                'dashboard_info' => $response->getDashboardInfo(),
            ], Response::HTTP_OK);
        } catch (\RuntimeException $exception) {
            return $this->handleRuntimeException($exception);
        } catch (\Throwable $throwable) {
            $this->logger->error('Unexpected error fetching dashboard info', [
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
        }

        return new JsonResponse([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }
}
