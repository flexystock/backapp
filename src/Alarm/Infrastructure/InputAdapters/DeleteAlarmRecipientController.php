<?php

namespace App\Alarm\Infrastructure\InputAdapters;

use App\Alarm\Application\InputPorts\DeleteAlarmRecipientUseCaseInterface;
use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteAlarmRecipientController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly DeleteAlarmRecipientUseCaseInterface $deleteAlarmRecipientUseCase,
        private readonly LoggerInterface $logger,
        PermissionService $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/alarm-recipients/{id}', name: 'api_alarm_recipients_delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/alarm-recipients/{id}',
        summary: 'Elimina un destinatario de alarma',
        tags: ['Alarm Recipients'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID del destinatario',
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'uuidClient',
                in: 'query',
                required: true,
                description: 'UUID del cliente',
                schema: new OA\Schema(type: 'string', format: 'uuid')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Destinatario eliminado correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'message', type: 'string', example: 'RECIPIENT_DELETED'),
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Parámetros inválidos'),
            new OA\Response(response: 401, description: 'Usuario no autenticado'),
            new OA\Response(response: 403, description: 'Permisos insuficientes'),
            new OA\Response(response: 404, description: 'Destinatario no encontrado'),
        ]
    )]
    public function __invoke(int $id, Request $request): JsonResponse
    {
        try {
            $permissionCheck = $this->checkPermissionJson('alarm.update', 'No tienes permisos para eliminar destinatarios de alarmas');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            $uuidClient = $request->query->get('uuidClient');

            if (!$uuidClient) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'El parámetro uuidClient es obligatorio.',
                ], Response::HTTP_BAD_REQUEST);
            }

            $result = $this->deleteAlarmRecipientUseCase->execute($id, $uuidClient);

            if (!$result) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'RECIPIENT_NOT_FOUND',
                ], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'status' => 'success',
                'message' => 'RECIPIENT_DELETED',
            ], Response::HTTP_OK);
        } catch (\RuntimeException $exception) {
            return $this->handleRuntimeException($exception);
        } catch (\Throwable $throwable) {
            $this->logger->error('Unexpected error deleting alarm recipient', [
                'exception' => $throwable->getMessage(),
                'id' => $id,
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

        if ('RECIPIENT_NOT_FOUND' === $message) {
            $statusCode = Response::HTTP_NOT_FOUND;
        }

        return new JsonResponse([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }
}
