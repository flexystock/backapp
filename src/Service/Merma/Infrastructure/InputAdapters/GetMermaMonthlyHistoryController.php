<?php

namespace App\Service\Merma\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Service\Merma\Application\DTO\GetMermaMonthlyHistoryRequest;
use App\Service\Merma\Application\InputPorts\GetMermaMonthlyHistoryUseCaseInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GetMermaMonthlyHistoryController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly GetMermaMonthlyHistoryUseCaseInterface $useCase,
        private readonly ValidatorInterface                     $validator,
        private readonly LoggerInterface                        $logger,
        PermissionService                                       $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/merma/history', name: 'api_merma_history', methods: ['POST'])]
    #[OA\Get(
        path: '/api/merma/history',
        summary: 'Obtiene el historial de informes mensuales de merma para una balanza y producto',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'scaleId', 'productId'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'scaleId', type: 'integer', example: 1),
                    new OA\Property(property: 'productId', type: 'integer', example: 1),
                    new OA\Property(property: 'limit', type: 'integer', example: 12, default: 12),
                ]
            )
        ),
        tags: ['Merma'],
        responses: [
            new OA\Response(response: 200, description: 'Historial recuperado correctamente'),
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
            $scaleId    = isset($data['scaleId']) ? (int) $data['scaleId'] : 0;
            $productId  = isset($data['productId']) ? (int) $data['productId'] : 0;
            $limit      = isset($data['limit']) ? (int) $data['limit'] : 12;

            if (empty($uuidClient)) {
                return new JsonResponse(['status' => 'error', 'message' => 'REQUIRED_CLIENT_ID'], Response::HTTP_BAD_REQUEST);
            }

            if (empty($scaleId)) {
                return new JsonResponse(['status' => 'error', 'message' => 'REQUIRED_SCALE_ID'], Response::HTTP_BAD_REQUEST);
            }

            if (empty($productId)) {
                return new JsonResponse(['status' => 'error', 'message' => 'REQUIRED_PRODUCT_ID'], Response::HTTP_BAD_REQUEST);
            }

            $dto    = new GetMermaMonthlyHistoryRequest($uuidClient, $scaleId, $productId, $limit);
            $errors = $this->validator->validate($dto);
            if (count($errors) > 0) {
                return $this->validationErrorResponse($errors);
            }

            $reports = $this->useCase->execute($dto);

            return new JsonResponse([
                'status'  => 'success',
                'message' => 'MERMA_HISTORY_RETRIEVED',
                'reports' => array_map(fn($r) => [
                    'report_id'         => $r->reportId,
                    'product_id'        => $r->productId,
                    'scale_id'          => $r->scaleId,
                    'period_label'      => $r->periodLabel,
                    'input_kg'          => $r->inputKg,
                    'consumed_kg'       => $r->consumedKg,
                    'anomaly_kg'        => $r->anomalyKg,
                    'actual_waste_kg'   => $r->actualWasteKg,
                    'waste_pct'         => $r->wastePct,
                    'waste_cost_euros'  => $r->wasteCostEuros,
                    'saved_vs_baseline' => $r->savedVsBaseline,
                    'status'            => $r->status,
                ], $reports),
            ], Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return $this->handleRuntimeException($e);
        } catch (\Throwable $e) {
            $this->logger->error('Unexpected error fetching merma history', ['exception' => $e->getMessage()]);
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