<?php

namespace App\Alarm\Infrastructure\InputAdapters;

use App\Alarm\Application\InputPorts\GetAlarmRecipientsUseCaseInterface;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetAlarmRecipientsController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly GetAlarmRecipientsUseCaseInterface $getAlarmRecipientsUseCase,
        private readonly LoggerInterface $logger,
        PermissionService $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/alarm-recipients', name: 'api_alarm_recipients_list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/alarm-recipients',
        summary: 'Lista los destinatarios de alarmas para un cliente',
        tags: ['Alarm Recipients'],
        parameters: [
            new OA\Parameter(
                name: 'uuidClient',
                in: 'query',
                required: true,
                description: 'UUID del cliente',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
            new OA\Parameter(
                name: 'alarmType',
                in: 'query',
                required: false,
                description: 'ID del tipo de alarma (1=stock, 2=horario, 3=holiday)',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de destinatarios',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(
                            property: 'recipients',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'email', type: 'string', example: 'admin@example.com'),
                                    new OA\Property(property: 'alarmTypeId', type: 'integer', example: 1),
                                ]
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Parámetros inválidos'),
            new OA\Response(response: 401, description: 'Usuario no autenticado'),
            new OA\Response(response: 403, description: 'Permisos insuficientes'),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $permissionCheck = $this->checkPermissionJson('alarm.view', 'No tienes permisos para ver los destinatarios de alarmas');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            $uuidClient = $request->query->get('uuidClient');
            $alarmType = $request->query->get('alarmType');

            if (!$uuidClient) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'El parámetro uuidClient es obligatorio.',
                ], Response::HTTP_BAD_REQUEST);
            }

            $alarmTypeId = $alarmType ? (int) $alarmType : null;

            $recipients = $this->getAlarmRecipientsUseCase->execute($uuidClient, $alarmTypeId);

            $recipientsData = array_map(function ($recipient) {
                return [
                    'id' => $recipient->getId(),
                    'email' => $recipient->getEmail(),
                    'alarmTypeId' => $recipient->getAlarmType()->getId(),
                    'alarmTypeName' => $recipient->getAlarmType()->getTypeName(),
                ];
            }, $recipients);

            return new JsonResponse([
                'status' => 'success',
                'recipients' => $recipientsData,
            ], Response::HTTP_OK);
        } catch (\RuntimeException $exception) {
            return $this->handleRuntimeException($exception);
        } catch (\Throwable $throwable) {
            $this->logger->error('Unexpected error retrieving alarm recipients', [
                'exception' => $throwable->getMessage(),
            ]);

            return new JsonResponse([
                'status' => 'error',
                'message' => 'UNEXPECTED_ERROR',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
