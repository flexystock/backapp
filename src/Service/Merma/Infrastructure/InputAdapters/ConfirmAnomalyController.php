<?php

namespace App\Service\Merma\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Service\Merma\Application\DTO\ConfirmAnomalyRequest;
use App\Service\Merma\Application\InputPorts\ConfirmAnomalyUseCaseInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ConfirmAnomalyController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly ConfirmAnomalyUseCaseInterface $useCase,
        private readonly ValidatorInterface             $validator,
        private readonly LoggerInterface                $logger,
        PermissionService                               $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/merma/event/{id}/confirm', name: 'api_merma_event_confirm', methods: ['POST'])]
    #[OA\Post(
        path: '/api/merma/event/{id}/confirm',
        summary: 'Confirma una anomalía como sustracción real',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid'),
                ]
            )
        ),
        tags: ['Merma'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Anomalía confirmada'),
            new OA\Response(response: 400, description: 'Datos inválidos'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'Sin permisos'),
            new OA\Response(response: 404, description: 'Cliente o evento no encontrado'),
        ]
    )]
    public function __invoke(Request $request, int $id): JsonResponse
    {
        try {
            $permissionCheck = $this->checkPermissionJson('merma.manage', 'No tienes permisos para gestionar la merma');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            $payload    = json_decode($request->getContent(), true) ?? [];
            $uuidClient = $payload['uuidClient'] ?? '';

            if (empty($uuidClient)) {
                return new JsonResponse(['status' => 'error', 'message' => 'REQUIRED_CLIENT_ID'], Response::HTTP_BAD_REQUEST);
            }

            $dto    = new ConfirmAnomalyRequest($uuidClient, $id);
            $errors = $this->validator->validate($dto);
            if (count($errors) > 0) {
                return $this->validationErrorResponse($errors);
            }

            $this->useCase->execute($dto);

            return new JsonResponse(['status' => 'success', 'message' => 'ANOMALY_CONFIRMED'], Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return $this->handleRuntimeException($e);
        } catch (\Throwable $e) {
            $this->logger->error('Unexpected error confirming anomaly', ['exception' => $e->getMessage()]);
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
            'CLIENT_NOT_FOUND', 'EVENT_NOT_FOUND' => Response::HTTP_NOT_FOUND,
            default                               => Response::HTTP_BAD_REQUEST,
        };
        return new JsonResponse(['status' => 'error', 'message' => $message], $statusCode);
    }
}