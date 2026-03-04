<?php

namespace App\Service\Merma\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Service\Merma\Application\DTO\UpdateMermaConfigRequest;
use App\Service\Merma\Application\InputPorts\UpdateMermaConfigUseCaseInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UpdateMermaConfigController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly UpdateMermaConfigUseCaseInterface $useCase,
        private readonly ValidatorInterface                $validator,
        private readonly LoggerInterface                   $logger,
        PermissionService                                  $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/merma/config/{productId}', name: 'api_merma_config_update', methods: ['PUT'])]
    #[OA\Put(
        path: '/api/merma/config/{productId}',
        summary: 'Crea o actualiza la configuración de merma de un producto',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'rendimientoEsperadoPct', 'serviceStart', 'serviceEnd', 'anomalyThresholdKg', 'alertOnAnomaly'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'rendimientoEsperadoPct', type: 'integer', minimum: 0, maximum: 100, example: 80),
                    new OA\Property(property: 'serviceStart', type: 'string', example: '09:00'),
                    new OA\Property(property: 'serviceEnd', type: 'string', example: '23:59'),
                    new OA\Property(property: 'anomalyThresholdKg', type: 'number', format: 'float', example: 0.200),
                    new OA\Property(property: 'alertOnAnomaly', type: 'boolean', example: true),
                ]
            )
        ),
        tags: ['Merma'],
        responses: [
            new OA\Response(response: 200, description: 'Configuración actualizada correctamente'),
            new OA\Response(response: 400, description: 'Datos inválidos'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'Sin permisos'),
            new OA\Response(response: 404, description: 'Cliente o producto no encontrado'),
        ]
    )]
    public function __invoke(Request $request, int $productId): JsonResponse
    {
        try {
            $permissionCheck = $this->checkPermissionJson('merma.manage', 'No tienes permisos para gestionar la merma');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            $payload = json_decode($request->getContent(), true) ?? [];

            $uuidClient             = $payload['uuidClient'] ?? '';
            $rendimientoEsperadoPct = isset($payload['rendimientoEsperadoPct']) ? (int) $payload['rendimientoEsperadoPct'] : 80;
            $serviceStart           = $payload['serviceStart'] ?? '09:00';
            $serviceEnd             = $payload['serviceEnd'] ?? '23:59';
            $anomalyThresholdKg     = isset($payload['anomalyThresholdKg']) ? (float) $payload['anomalyThresholdKg'] : 0.200;
            $alertOnAnomaly         = (bool) ($payload['alertOnAnomaly'] ?? true);

            if (empty($uuidClient)) {
                return new JsonResponse(['status' => 'error', 'message' => 'REQUIRED_CLIENT_ID'], Response::HTTP_BAD_REQUEST);
            }

            $dto = new UpdateMermaConfigRequest(
                $uuidClient,
                $productId,
                $rendimientoEsperadoPct,
                $serviceStart,
                $serviceEnd,
                $anomalyThresholdKg,
                $alertOnAnomaly,
            );

            $errors = $this->validator->validate($dto);
            if (count($errors) > 0) {
                return $this->validationErrorResponse($errors);
            }

            $config = $this->useCase->execute($dto);

            return new JsonResponse([
                'status'  => 'success',
                'message' => 'MERMA_CONFIG_UPDATED',
                'config'  => $config,
            ], Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return $this->handleRuntimeException($e);
        } catch (\Throwable $e) {
            $this->logger->error('Unexpected error updating merma config', ['exception' => $e->getMessage()]);
            return new JsonResponse(['status' => 'error', 'message' => 'UNEXPECTED_ERROR'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function validationErrorResponse(ConstraintViolationListInterface $errors): JsonResponse
    {
        $messages = [];
        foreach ($errors as $error) {
            $messages[$error->getPropertyPath()] = $error->getMessage();
        }
        return new JsonResponse(['status' => 'error', 'message' => 'INVALID_DATA', 'errors' => $messages], Response::HTTP_BAD_REQUEST);
    }

    private function handleRuntimeException(\RuntimeException $e): JsonResponse
    {
        $message    = $e->getMessage();
        $statusCode = match (true) {
            $message === 'CLIENT_NOT_FOUND'           => Response::HTTP_NOT_FOUND,
            str_starts_with($message, 'PRODUCT_NOT_FOUND') => Response::HTTP_NOT_FOUND,
            $message === 'INVALID_TIME_FORMAT'        => Response::HTTP_BAD_REQUEST,
            default                                   => Response::HTTP_BAD_REQUEST,
        };
        return new JsonResponse(['status' => 'error', 'message' => $message], $statusCode);
    }
}
