<?php

namespace App\Service\Merma\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Service\Merma\Application\DTO\GetScalesByProductRequest;
use App\Service\Merma\Application\InputPorts\GetScalesByProductUseCaseInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GetScalesByProductController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private readonly GetScalesByProductUseCaseInterface $useCase,
        private readonly ValidatorInterface                 $validator,
        private readonly LoggerInterface                    $logger,
        PermissionService                                   $permissionService,
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/merma/scales-by-product', name: 'api_merma_scales_by_product', methods: ['POST'])]
    #[OA\Post(
        path: '/api/merma/scales-by-product',
        summary: 'Obtiene las balanzas asociadas a un producto',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['uuidClient', 'productId'],
                properties: [
                    new OA\Property(property: 'uuidClient', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'productId', type: 'integer', example: 1),
                ]
            )
        ),
        tags: ['Merma'],
        responses: [
            new OA\Response(response: 200, description: 'Listado de balanzas asociadas al producto'),
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
            $productId  = isset($data['productId']) ? (int) $data['productId'] : 0;

            if (empty($uuidClient)) {
                return new JsonResponse(['status' => 'error', 'message' => 'REQUIRED_CLIENT_ID'], Response::HTTP_BAD_REQUEST);
            }

            if ($productId <= 0) {
                return new JsonResponse(['status' => 'error', 'message' => 'REQUIRED_PRODUCT_ID'], Response::HTTP_BAD_REQUEST);
            }

            $dto    = new GetScalesByProductRequest($uuidClient, $productId);
            $errors = $this->validator->validate($dto);
            if (count($errors) > 0) {
                return $this->validationErrorResponse($errors);
            }

            $response = $this->useCase->execute($dto);

            return new JsonResponse([
                'status'  => 'success',
                'message' => 'SCALES_BY_PRODUCT_RETRIEVED',
                'scales'  => $response->scales,
            ], Response::HTTP_OK);
        } catch (\RuntimeException $e) {
            return $this->handleRuntimeException($e);
        } catch (\Throwable $e) {
            $this->logger->error('Unexpected error fetching scales by product', ['exception' => $e->getMessage()]);
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
