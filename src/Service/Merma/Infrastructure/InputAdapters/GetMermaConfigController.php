<?php

namespace App\Service\Merma\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Service\Merma\Application\DTO\GetMermaConfigRequest;
use App\Service\Merma\Application\InputPorts\GetMermaConfigUseCaseInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GetMermaConfigController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly GetMermaConfigUseCaseInterface $useCase,
        private readonly ValidatorInterface             $validator,
        private readonly LoggerInterface                $logger,
        PermissionService                               $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/merma/config/get', name: 'api_merma_config_get', methods: ['POST'])]
    #[OA\Get(
        path: '/api/merma/config/{productId}',
        summary: 'Obtiene la configuración de merma de un producto',
        parameters: [
            new OA\Parameter(name: 'productId', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'uuidClient', in: 'query', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        tags: ['Merma'],
        responses: [
            new OA\Response(response: 200, description: 'Configuración recuperada correctamente'),
            new OA\Response(response: 400, description: 'Datos inválidos'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 403, description: 'Sin permisos'),
            new OA\Response(response: 404, description: 'Cliente o configuración no encontrada'),
        ]
    )]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $permissionCheck = $this->checkPermissionJson('merma.view', 'No tienes permisos para consultar la merma');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            $data      = json_decode($request->getContent(), true) ?? [];
            $uuidClient = $data['uuidClient'] ?? '';
            $productId  = $data['productId'] ?? null;


            if (empty($uuidClient)) {
                return new JsonResponse(['status' => 'error', 'message' => 'REQUIRED_CLIENT_ID'], Response::HTTP_BAD_REQUEST);
            }

            if (empty($productId) || !is_int($productId)) {
                return new JsonResponse(['status' => 'error', 'message' => 'REQUIRED_PRODUCT_ID'], Response::HTTP_BAD_REQUEST);
            }

            $dto    = new GetMermaConfigRequest($uuidClient, $productId);
            $errors = $this->validator->validate($dto);
            if (count($errors) > 0) {
                return $this->validationErrorResponse($errors);
            }

            $config = $this->useCase->execute($dto);

            return new JsonResponse([
                'status'  => 'success',
                'message' => 'MERMA_CONFIG_RETRIEVED',
                'config'  => $config,
            ], Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return $this->handleRuntimeException($e);
        } catch (\Throwable $e) {
            $this->logger->error('Unexpected error fetching merma config', ['exception' => $e->getMessage()]);
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
            'CLIENT_NOT_FOUND', 'MERMA_CONFIG_NOT_FOUND' => Response::HTTP_NOT_FOUND,
            default                                      => Response::HTTP_BAD_REQUEST,
        };
        return new JsonResponse(['status' => 'error', 'message' => $message], $statusCode);
    }
}
