<?php

namespace App\Alarm\Infrastructure\InputAdapters;

use App\Alarm\Application\DTO\GetBusinessHoursRequest;
use App\Alarm\Application\InputPorts\GetBusinessHoursUseCaseInterface;
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

class GetAlarmsBusinessHoursController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly GetBusinessHoursUseCaseInterface $getBusinessHoursUseCase,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly LoggerInterface $logger,
        PermissionService $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/alarm/business-hours', name: 'api_alarm_get_business_hours', methods: ['GET'])]
    #[OA\Get(
        path: '/api/alarm/business-hours',
        summary: 'Obtiene los horarios comerciales configurados',
        parameters: [
            new OA\Parameter(
                name: 'uuidClient',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'uuid'),
                description: 'UUID del cliente para recuperar su configuración de horarios'
            ),
        ],
        tags: ['Alarm'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Horarios comerciales recuperados correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'BUSINESS_HOURS_RETRIEVED'),
                        new OA\Property(
                            property: 'business_hours',
                            type: 'array',
                            items: new OA\Items(
                                type: 'object',
                                properties: [
                                    new OA\Property(property: 'day_of_week', type: 'integer', example: 1),
                                    new OA\Property(property: 'start_time', type: 'string', nullable: true, example: '08:00:00'),
                                    new OA\Property(property: 'end_time', type: 'string', nullable: true, example: '16:00:00'),
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
            $permissionCheck = $this->checkPermissionJson('alarm.view', 'No tienes permisos para consultar las alarmas');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            // Unificamos entrada: primero query, si no hay miramos body
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

            /** @var GetBusinessHoursRequest $dto */
            $dto = $this->serializer->deserialize(
                json_encode(['uuidClient' => $payload['uuidClient']], JSON_THROW_ON_ERROR),
                GetBusinessHoursRequest::class,
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

            $response = $this->getBusinessHoursUseCase->execute($dto);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'BUSINESS_HOURS_RETRIEVED',
                'business_hours' => $response->getBusinessHours(),
                'checkBusinessHours' => $response->getCheckoutOfHours()
            ], Response::HTTP_OK);
        } catch (\RuntimeException $exception) {
            return $this->handleRuntimeException($exception);
        } catch (\Throwable $throwable) {
            $this->logger->error('Unexpected error fetching business hours', [
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
