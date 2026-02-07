<?php

namespace App\Alarm\Infrastructure\InputAdapters;

use App\Alarm\Application\DTO\CreateAlarmRecipientRequest;
use App\Alarm\Application\InputPorts\CreateAlarmRecipientUseCaseInterface;
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

class CreateAlarmRecipientController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly CreateAlarmRecipientUseCaseInterface $createAlarmRecipientUseCase,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly LoggerInterface $logger,
        PermissionService $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/alarm-recipients', name: 'api_alarm_recipients_create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/alarm-recipients',
        summary: 'Crea un nuevo destinatario de alarma',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'alarmTypeId', 'email'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid', example: 'c014a415-4113-49e5-80cb-cc3158c15236'),
                    new OA\Property(property: 'alarmTypeId', type: 'integer', enum: [1, 2, 3], example: 1, description: '1=stock, 2=horario, 3=holiday'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@example.com'),
                ]
            )
        ),
        tags: ['Alarm Recipients'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Destinatario creado correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'RECIPIENT_CREATED'),
                        new OA\Property(property: 'recipient', type: 'object', properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'email', type: 'string', example: 'admin@example.com'),
                            new OA\Property(property: 'alarmTypeId', type: 'integer', example: 1),
                        ]),
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Datos inválidos'),
            new OA\Response(response: 401, description: 'Usuario no autenticado'),
            new OA\Response(response: 403, description: 'Permisos insuficientes'),
            new OA\Response(response: 409, description: 'El destinatario ya existe'),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $permissionCheck = $this->checkPermissionJson('alarm.update', 'No tienes permisos para crear destinatarios de alarmas');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            /** @var CreateAlarmRecipientRequest $createRequest */
            $createRequest = $this->serializer->deserialize(
                $request->getContent(),
                CreateAlarmRecipientRequest::class,
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

            $recipient = $this->createAlarmRecipientUseCase->execute($createRequest);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'RECIPIENT_CREATED',
                'recipient' => [
                    'id' => $recipient->getId(),
                    'email' => $recipient->getEmail(),
                    'alarmTypeId' => $recipient->getAlarmType()->getId(),
                    'alarmTypeName' => $recipient->getAlarmType()->getTypeName(),
                ],
            ], Response::HTTP_CREATED);
        } catch (\RuntimeException $exception) {
            return $this->handleRuntimeException($exception);
        } catch (\Throwable $throwable) {
            $this->logger->error('Unexpected error creating alarm recipient', [
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

        if ('ALARM_TYPE_NOT_FOUND' === $message) {
            $statusCode = Response::HTTP_NOT_FOUND;
        } elseif (str_contains($message, 'Duplicate entry')) {
            $statusCode = Response::HTTP_CONFLICT;
            $message = 'RECIPIENT_ALREADY_EXISTS';
        }

        return new JsonResponse([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }
}
