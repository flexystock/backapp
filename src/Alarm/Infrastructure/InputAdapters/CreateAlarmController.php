<?php

namespace App\Alarm\Infrastructure\InputAdapters;

use App\Alarm\Application\DTO\CreateAlarmRequest;
use App\Alarm\Application\InputPorts\CreateAlarmUseCaseInterface;
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

class CreateAlarmController extends AbstractController
{
    private CreateAlarmUseCaseInterface $createAlarmUseCase;
    private LoggerInterface $logger;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(LoggerInterface $logger, CreateAlarmUseCaseInterface $createAlarmUseCase,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
    ) {
        $this->logger = $logger;
        $this->createAlarmUseCase = $createAlarmUseCase;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    #[Route('/api/alarm_create', name: 'api_alarm_create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/alarm_create',
        summary: 'Crear un nuevo alarma para un cliente',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'name', 'type', 'percentageThreshold'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid', example: 'c014a415-4113-49e5-80cb-cc3158c15236'),
                    new OA\Property(property: 'name', type: 'string', example: 'Nuevo nombre de la alarma'),
                    new OA\Property(property: 'type', type: 'string', enum: ['stock', 'horario'], example: 'stock'),
                    new OA\Property(property: 'percentageThreshold', type: 'number', format: 'float', example: 0.5),
                ],
                type: 'object'
            )
        ),
        tags: ['Alarm'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Alarma creada con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'alarm',
                            properties: [
                                new OA\Property(property: 'uuid', type: 'string', format: 'uuid', example: '9a6ae1c0-3bc6-41c8-975a-4de5b4357666'),
                                new OA\Property(property: 'name', type: 'string', example: 'alarma1'),
                            ],
                            type: 'object',
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Faltan campos uuid_client o uuid_product',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Missing required fields: uuid_client or uuid_product'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'No autenticado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Unauthorized'),
                    ],
                    type: 'object',
                )
            ),
            new OA\Response(
                response: 403,
                description: 'El usuario no tiene acceso al cliente especificado',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Access denied to the specified client'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Error interno del servidor',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Internal Server Error'),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function invoke(Request $request): JsonResponse
    {
        try {
            //exit('antes del create');
            // 1) Deserializar el JSON al DTO CreateAlarmRequest
            $createRequest = $this->serializer->deserialize(
                $request->getContent(),
                CreateAlarmRequest::class,
                'json'
            );

            // 2) Validar el DTO con Symfony Validator
            $errors = $this->validator->validate($createRequest);
            if (count($errors) > 0) {
                $errorMessages = $this->formatValidationErrors($errors);

                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'INVALID_DATA',
                    'errors' => $errorMessages,
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            // 3) Asignar manualmente (porque el user no llega en el JSON)
            $user = $this->getUser();
            if (!$user) {
                return $this->jsonError('USER_NOT_AUTHENTICATED', JsonResponse::HTTP_UNAUTHORIZED);
            }

            // 4) Asignar el userCreation
            $createRequest->setUuidUserCreation($user->getUuid());

            // 5) Asignar fecha de creación
            $createRequest->setDatehourCreation(new \DateTime());

            //die("antes del caso de uso");
            // 6) Ejecutar el caso de uso
            $response = $this->createAlarmUseCase->execute($createRequest);

            // 7) Respuesta exitosa
            return new JsonResponse([
                'status' => 'success',
                'message' => 'ALARM_CREATED_SUCCESSFULLY',
                'alarm' => $response->getAlarm(), // Por ej. array con uuid, name...
            ], $response->getStatusCode());
        } catch (\RuntimeException $e) {
            // Errores de dominio esperados (p.ej. "ALARM_NOT_FOUND", "CLIENT_NOT_FOUND", etc.)
            if ('ALARM_NOT_FOUND' === $e->getMessage()) {
                return $this->jsonError('ALARM_NOT_FOUND', JsonResponse::HTTP_NOT_FOUND);
            }
            if ('CLIENT_NOT_FOUND' === $e->getMessage()) {
                return $this->jsonError('CLIENT_NOT_FOUND', JsonResponse::HTTP_NOT_FOUND);
            }
            if ('USER_NOT_AUTHENTICATED' === $e->getMessage()) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'USER_NOT_AUTHENTICATED',
                ], Response::HTTP_UNAUTHORIZED);
            }
            // etc. Manejo 403, 409, etc.

            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            // Errores inesperados
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Formatea la lista de errores de validación en un array asociativo.
     */
    private function formatValidationErrors(ConstraintViolationListInterface $errors): array
    {
        $errorMessages = [];
        foreach ($errors as $error) {
            $field = $error->getPropertyPath();
            $message = $error->getMessage();
            $errorMessages[$field] = $message;
        }

        return $errorMessages;
    }
}
