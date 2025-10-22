<?php

namespace App\Alarm\Infrastructure\InputAdapters;

use App\Alarm\Application\DTO\GetAlarmBatteryShelveRequest;
use App\Alarm\Application\InputPorts\GetAlarmBatteryShelveUseCaseInterface;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GetAlarmBatteryShelveController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly GetAlarmBatteryShelveUseCaseInterface $getAlarmBatteryShelveUseCase,
        private readonly ValidatorInterface $validator,
        private readonly LoggerInterface $logger,
        PermissionService $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/alarm/battery-shelve', name: 'api_alarm_get_battery_shelve', methods: ['GET'])]
    #[OA\Get(
        path: '/api/alarm/battery-shelve',
        summary: 'Obtiene la configuración de la alarma de batería en estantería para un cliente',
        parameters: [
            new OA\Parameter(
                name: 'uuidClient',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid'),
                description: 'UUID del cliente para recuperar su configuración'
            ),
        ],
        tags: ['Alarm'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Configuración de alarma de batería recuperada correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'BATTERY_SHELVE_ALARM_RETRIEVED'),
                        new OA\Property(property: 'checkBatteryShelve', type: 'boolean', example: true),
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
            $permissionCheck = $this->checkPermissionJson('alarm.view', 'No tienes permisos para consultar las alarmas');
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

            $uuidClient = $payload['uuidClient'] ?? null;
            if (!is_string($uuidClient) || '' === trim($uuidClient)) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'REQUIRED_CLIENT_ID',
                ], Response::HTTP_BAD_REQUEST);
            }

            $dto = new GetAlarmBatteryShelveRequest($uuidClient);
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

            $response = $this->getAlarmBatteryShelveUseCase->execute($dto);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'BATTERY_SHELVE_ALARM_RETRIEVED',
                'checkBatteryShelve' => $response->isCheckBatteryShelveEnabled(),
            ], Response::HTTP_OK);
        } catch (\RuntimeException $exception) {
            return $this->handleRuntimeException($exception);
        } catch (\Throwable $throwable) {
            $this->logger->error('Unexpected error fetching battery shelve alarm configuration', [
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
