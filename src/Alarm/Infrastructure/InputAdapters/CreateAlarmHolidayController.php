<?php

namespace App\Alarm\Infrastructure\InputAdapters;

use App\Alarm\Application\DTO\CreateAlarmHolidayRequest;
use App\Alarm\Application\InputPorts\CreateAlarmHolidayUseCaseInterface;
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

class CreateAlarmHolidayController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly CreateAlarmHolidayUseCaseInterface $createAlarmHolidayUseCase,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly LoggerInterface $logger,
        PermissionService $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/alarm/holiday', name: 'api_alarm_create_holiday', methods: ['POST'])]
    #[OA\Post(
        path: '/api/alarm/holiday',
        summary: 'Registra un día festivo para un cliente',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'holidayDate'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid', example: 'c014a415-4113-49e5-80cb-cc3158c15236'),
                    new OA\Property(property: 'holidayDate', type: 'string', format: 'date', example: '2024-12-25'),
                    new OA\Property(property: 'name', type: 'string', nullable: true, example: 'Navidad'),
                ]
            )
        ),
        tags: ['Alarm'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Festivo registrado correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'holiday', type: 'object', properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'holiday_date', type: 'string', example: '2024-12-25'),
                            new OA\Property(property: 'name', type: 'string', nullable: true, example: 'Navidad'),
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
            $permissionCheck = $this->checkPermissionJson('alarm.update', 'No tienes permisos para actualizar las alarmas');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            /** @var CreateAlarmHolidayRequest $createRequest */
            $createRequest = $this->serializer->deserialize(
                $request->getContent(),
                CreateAlarmHolidayRequest::class,
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
            //var_dump($createRequest);
            //die("llegamos");
            $response = $this->createAlarmHolidayUseCase->execute($createRequest);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'HOLIDAY_REGISTERED',
                'holiday' => $response->getHoliday(),
            ], Response::HTTP_CREATED);
        } catch (\RuntimeException $exception) {
            return $this->handleRuntimeException($exception);
        } catch (\Throwable $throwable) {
            $this->logger->error('Unexpected error registering holiday', [
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
        } elseif ('INVALID_HOLIDAY_DATE' === $message) {
            $statusCode = Response::HTTP_BAD_REQUEST;
        }

        return new JsonResponse([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }
}
