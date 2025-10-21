<?php

namespace App\Alarm\Infrastructure\InputAdapters;

use App\Alarm\Application\DTO\CreateAlarmBatteryShelveRequest;
use App\Alarm\Application\InputPorts\CreateAlarmBatteryShelveUseCaseInterface;
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

class CreateAlarmBatteryShelveController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly CreateAlarmBatteryShelveUseCaseInterface $createAlarmBatteryShelveUseCase,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly LoggerInterface $logger,
        PermissionService $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/alarm/battery-shelve', name: 'api_alarm_create_battery_shelve', methods: ['POST'])]
    #[OA\Post(
        path: '/api/alarm/battery-shelve',
        summary: 'Configura la alarma de batería en estantería para un cliente',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'checkBatteryShelve'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid', example: 'c014a415-4113-49e5-80cb-cc3158c15236'),
                    new OA\Property(property: 'checkBatteryShelve', type: 'integer', enum: [0, 1], example: 1),
                ]
            )
        ),
        tags: ['Alarm'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Configuración de alarma de batería actualizada correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'BATTERY_SHELVE_ALARM_UPDATED'),
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
            $permissionCheck = $this->checkPermissionJson('alarm.update', 'No tienes permisos para actualizar las alarmas');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            /** @var CreateAlarmBatteryShelveRequest $createRequest */
            $createRequest = $this->serializer->deserialize(
                $request->getContent(),
                CreateAlarmBatteryShelveRequest::class,
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

            $response = $this->createAlarmBatteryShelveUseCase->execute($createRequest);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'BATTERY_SHELVE_ALARM_UPDATED',
                'checkBatteryShelve' => $response->isCheckBatteryShelveEnabled(),
            ], Response::HTTP_OK);
        } catch (\RuntimeException $exception) {
            return $this->handleRuntimeException($exception);
        } catch (\Throwable $throwable) {
            $this->logger->error('Unexpected error configuring battery shelve alarm', [
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
