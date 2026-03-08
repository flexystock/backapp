<?php

namespace App\Service\Merma\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Service\Merma\Application\DTO\GetAnomalyHistoryRequest;
use App\Service\Merma\Application\InputPorts\GetAnomalyHistoryUseCaseInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GetAnomalyHistoryController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly GetAnomalyHistoryUseCaseInterface $useCase,
        private readonly ValidatorInterface                $validator,
        private readonly LoggerInterface                   $logger,
        PermissionService                                  $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/merma/anomalies/history', name: 'api_merma_anomalies_history', methods: ['POST'])]
    #[OA\Post(
        path: '/api/merma/anomalies/history',
        summary: 'Obtiene el historial de anomalías resueltas (confirmadas o descartadas)',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'scaleId', 'productId'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'scaleId', type: 'integer'),
                    new OA\Property(property: 'productId', type: 'integer'),
                    new OA\Property(property: 'dateFrom', type: 'string', format: 'date-time', nullable: true),
                    new OA\Property(property: 'dateTo', type: 'string', format: 'date-time', nullable: true),
                ]
            )
        ),
        tags: ['Merma'],
        responses: [
            new OA\Response(response: 200, description: 'Historial de anomalías resueltas'),
            new OA\Response(response: 400, description: 'Datos inválidos'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'Sin permisos'),
            new OA\Response(response: 404, description: 'Cliente no encontrado'),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $permissionCheck = $this->checkPermissionJson('merma.view', 'No tienes permisos para consultar la merma');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            $data       = json_decode($request->getContent(), true) ?? [];
            $uuidClient = $data['uuidClient'] ?? '';
            $scaleId    = (int) ($data['scaleId'] ?? 0);
            $productId  = (int) ($data['productId'] ?? 0);

            if (empty($uuidClient)) {
                return new JsonResponse(['status' => 'error', 'message' => 'REQUIRED_CLIENT_ID'], Response::HTTP_BAD_REQUEST);
            }

            $dateFrom = isset($data['dateFrom']) && $data['dateFrom'] !== null
                ? new \DateTime($data['dateFrom'])
                : null;

            $dateTo = isset($data['dateTo']) && $data['dateTo'] !== null
                ? new \DateTime($data['dateTo'] . ' 23:59:59')
                : null;

            $dto    = new GetAnomalyHistoryRequest($uuidClient, $scaleId, $productId, $dateFrom, $dateTo);
            $errors = $this->validator->validate($dto);
            if (count($errors) > 0) {
                return $this->validationErrorResponse($errors);
            }

            $response = $this->useCase->execute($dto);

            return new JsonResponse([
                'status'    => 'success',
                'message'   => 'ANOMALY_HISTORY_RETRIEVED',
                'anomalies' => $response->anomalies,
            ], Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return $this->handleRuntimeException($e);
        } catch (\Throwable $e) {
            $this->logger->error('Unexpected error fetching anomaly history', ['exception' => $e->getMessage()]);
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
        $statusCode = match ($message) {
            'CLIENT_NOT_FOUND' => Response::HTTP_NOT_FOUND,
            default            => Response::HTTP_BAD_REQUEST,
        };
        return new JsonResponse(['status' => 'error', 'message' => $message], $statusCode);
    }
}
