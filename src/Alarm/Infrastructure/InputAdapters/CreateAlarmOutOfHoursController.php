<?php

namespace App\Alarm\Infrastructure\InputAdapters;

use App\Alarm\Application\DTO\CreateAlarmOutOfHoursRequest;
use App\Alarm\Application\InputPorts\CreateAlarmOutOfHoursUseCaseInterface;
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

class CreateAlarmOutOfHoursController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly CreateAlarmOutOfHoursUseCaseInterface $createAlarmOutOfHoursUseCase,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly LoggerInterface $logger,
        PermissionService $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/alarm/out-of-hours', name: 'api_alarm_create_out_of_hours', methods: ['POST'])]
    #[OA\Post(
        path: '/api/alarm/out-of-hours',
        summary: 'Configura el horario laboral de un cliente',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'businessHours', 'checkOutOfHours'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid', example: 'c014a415-4113-49e5-80cb-cc3158c15236'),
                    new OA\Property(
                        property: 'businessHours',
                        type: 'array',
                        items: new OA\Items(
                            type: 'object',
                            required: ['day_of_week'],
                            properties: [
                                new OA\Property(property: 'day_of_week', type: 'integer', minimum: 1, maximum: 7, example: 1),
                                new OA\Property(property: 'start_time', type: 'string', nullable: true, example: '08:00'),
                                new OA\Property(property: 'end_time', type: 'string', nullable: true, example: '16:00'),
                                new OA\Property(property: 'start_time2', type: 'string', nullable: true, example: '20:00'),
                                new OA\Property(property: 'end_time2', type: 'string', nullable: true, example: '23:00'),
                            ]
                        )
                    ),
                    new OA\Property(property: 'checkOutOfHours', type: 'integer', enum: [0, 1], example: 1),
                ]
            )
        ),
        tags: ['Alarm'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Horario laboral configurado correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(
                            property: 'businessHours',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'day_of_week', type: 'integer', example: 1),
                                    new OA\Property(property: 'start_time', type: 'string', example: '08:00:00'),
                                    new OA\Property(property: 'end_time', type: 'string', example: '16:00:00'),
                                    new OA\Property(property: 'start_time2', type: 'string', nullable: true, example: '20:00:00'),
                                    new OA\Property(property: 'end_time2', type: 'string', nullable: true, example: '23:00:00'),
                                ]
                            )
                        ),
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

            /** @var CreateAlarmOutOfHoursRequest $createRequest */
            $createRequest = $this->serializer->deserialize(
                $request->getContent(),
                CreateAlarmOutOfHoursRequest::class,
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

            $response = $this->createAlarmOutOfHoursUseCase->execute($createRequest);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'BUSINESS_HOURS_CONFIGURED',
                'uuidClient' => $response->getUuidClient(),
                'businessHours' => $response->getBusinessHours(),
                'checkOutOfHours' => $response->getCheckoutOfHours()
            ], Response::HTTP_OK);
        } catch (\RuntimeException $exception) {
            return $this->handleRuntimeException($exception);
        } catch (\Throwable $throwable) {
            $this->logger->error('Unexpected error configuring business hours', [
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
        } elseif (str_starts_with($message, 'INVALID_TIME_FORMAT')) {
            $statusCode = Response::HTTP_BAD_REQUEST;
        } elseif (str_starts_with($message, 'INCOMPLETE_') || str_starts_with($message, 'INVALID_')) {
            $statusCode = Response::HTTP_BAD_REQUEST;
        }

        return new JsonResponse([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }
}
