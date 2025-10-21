<?php

namespace App\Alarm\Infrastructure\InputAdapters;

use App\Alarm\Application\DTO\SyncAlarmHolidaysRequest;
use App\Alarm\Application\InputPorts\SyncAlarmHolidaysUseCaseInterface;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SyncAlarmHolidaysController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly SyncAlarmHolidaysUseCaseInterface $useCase,
        private readonly ValidatorInterface $validator,
        private readonly LoggerInterface $logger,
        PermissionService $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/alarm/holidays', name: 'api_alarm_sync_holidays', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/alarm/holidays',
        summary: 'Reemplaza (sincroniza) todos los días festivos de un cliente',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'holidays', 'checkHolidays'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid'),
                    new OA\Property(
                        property: 'holidays',
                        type: 'array',
                        items: new OA\Items(
                            type: 'object',
                            required: ['date'],
                            properties: [
                                new OA\Property(property: 'date', type: 'string', format: 'date', example: '2025-01-01'),
                                new OA\Property(property: 'name', type: 'string', nullable: true, example: 'Año Nuevo')
                            ]
                        )
                    ),
                    new OA\Property(property: 'checkHolidays', type: 'integer', enum: [0, 1])
                ]
            )
        ),
        tags: ['Alarm'],
        responses: [
            new OA\Response(response: 200, description: 'Festivos sincronizados correctamente'),
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

            $payload = json_decode($request->getContent(), true) ?? [];
            $uuidClient = $payload['uuidClient'] ?? null;
            $holidays   = $payload['holidays'] ?? null;
            $checkHolidays = $this->normalizeCheckValue($payload['checkHoliday'] ?? null);

            $errors = [];
            if (!is_string($uuidClient)) {
                $errors['uuidClient'] = 'REQUIRED_CLIENT_ID';
            }

            if (!is_array($holidays)) {
                $errors['holidays'] = 'REQUIRED_HOLIDAYS_ARRAY';
            }

            if (null === $checkHolidays) {
                $errors['checkHoliday'] = 'INVALID_CHECK_HOLIDAYS';
            }

            if ($errors !== []) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'INVALID_DATA',
                    'errors' => $errors,
                ], Response::HTTP_BAD_REQUEST);
            }

            $user = $this->getUser();
            if (!is_object($user) || !method_exists($user, 'getUuid')) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'USER_NOT_AUTHENTICATED',
                ], Response::HTTP_UNAUTHORIZED);
            }

            $dto = new SyncAlarmHolidaysRequest(
                uuidClient: $uuidClient,
                holidays: $holidays,
                uuidUser: $user->getUuid(),
                checkHolidays: $checkHolidays
            );

            // (Opcional) si añades constraints al DTO, valida aquí:
            // $errors = $this->validator->validate($dto);
            // if (count($errors) > 0) { ... }

            $response = $this->useCase->execute($dto);

            return new JsonResponse([
                'status' => 'success',
                'message' => 'HOLIDAYS_SYNCED',
                'holidays' => $response->getHolidays(), // lista final después de sync
                'checkHolidays' => $checkHolidays,
            ], Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            $status = match ($e->getMessage()) {
                'CLIENT_NOT_FOUND' => Response::HTTP_NOT_FOUND,
                'INVALID_HOLIDAY_DATE' => Response::HTTP_BAD_REQUEST,
                default => Response::HTTP_BAD_REQUEST
            };

            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], $status);
        } catch (\Throwable $t) {
            $this->logger->error('Unexpected error syncing holidays', ['exception' => $t->getMessage()]);
            return new JsonResponse([
                'status' => 'error',
                'message' => 'UNEXPECTED_ERROR',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    private function normalizeCheckValue(mixed $value): ?int
    {
        if (null === $value) {
            return null;
        }

        if (is_int($value) && ($value === 0 || $value === 1)) {
            return $value;
        }

        if (is_string($value) && ($value === '0' || $value === '1')) {
            return (int) $value;
        }

        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        return null;
    }
}
