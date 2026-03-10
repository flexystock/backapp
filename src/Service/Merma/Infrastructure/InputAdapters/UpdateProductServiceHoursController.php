<?php

namespace App\Service\Merma\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Service\Merma\Application\DTO\UpdateProductServiceHoursRequest;
use App\Service\Merma\Application\InputPorts\UpdateProductServiceHoursUseCaseInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateProductServiceHoursController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly UpdateProductServiceHoursUseCaseInterface $useCase,
        private readonly LoggerInterface                           $logger,
        PermissionService                                          $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/merma/config/hours', name: 'api_merma_config_hours_update', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/merma/config/hours',
        summary: 'Crea o actualiza las horas de servicio a nivel de producto',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'productId', 'hours'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'productId', type: 'integer', example: 1),
                    new OA\Property(property: 'hours', type: 'array', items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'dayOfWeek', type: 'integer', example: 1),
                            new OA\Property(property: 'startTime1', type: 'string', example: '09:00'),
                            new OA\Property(property: 'endTime1', type: 'string', example: '17:00'),
                            new OA\Property(property: 'startTime2', type: 'string', example: null, nullable: true),
                            new OA\Property(property: 'endTime2', type: 'string', example: null, nullable: true),
                        ]
                    )),
                ]
            )
        ),
        tags: ['Merma'],
        responses: [
            new OA\Response(response: 200, description: 'Horas de servicio actualizadas correctamente'),
            new OA\Response(response: 400, description: 'Datos inválidos'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'Sin permisos'),
            new OA\Response(response: 404, description: 'Cliente o producto no encontrado'),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $permissionCheck = $this->checkPermissionJson('merma.manage', 'No tienes permisos para gestionar la merma');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            $payload    = json_decode($request->getContent(), true) ?? [];
            $uuidClient = $payload['uuidClient'] ?? '';
            $productId  = $payload['productId'] ?? null;

            if (empty($uuidClient)) {
                return new JsonResponse(['status' => 'error', 'message' => 'REQUIRED_CLIENT_ID'], Response::HTTP_BAD_REQUEST);
            }

            if (empty($productId) || !is_int($productId)) {
                return new JsonResponse(['status' => 'error', 'message' => 'REQUIRED_PRODUCT_ID'], Response::HTTP_BAD_REQUEST);
            }

            $hours = is_array($payload['hours'] ?? null) ? $payload['hours'] : [];

            $dto = new UpdateProductServiceHoursRequest($uuidClient, $productId, $hours);

            $this->useCase->execute($dto);

            return new JsonResponse([
                'status'  => 'success',
                'message' => 'HOURS_UPDATED',
            ], Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return $this->handleRuntimeException($e);
        } catch (\Throwable $e) {
            $this->logger->error('Unexpected error updating product service hours', ['exception' => $e->getMessage()]);
            return new JsonResponse(['status' => 'error', 'message' => 'UNEXPECTED_ERROR'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function handleRuntimeException(\RuntimeException $e): JsonResponse
    {
        $message    = $e->getMessage();
        $statusCode = match (true) {
            $message === 'CLIENT_NOT_FOUND'                => Response::HTTP_NOT_FOUND,
            str_starts_with($message, 'PRODUCT_NOT_FOUND') => Response::HTTP_NOT_FOUND,
            default                                        => Response::HTTP_BAD_REQUEST,
        };
        return new JsonResponse(['status' => 'error', 'message' => $message], $statusCode);
    }
}
