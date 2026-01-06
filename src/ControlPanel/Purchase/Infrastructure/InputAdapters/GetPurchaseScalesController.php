<?php

declare(strict_types=1);

namespace App\ControlPanel\Purchase\Infrastructure\InputAdapters;

use App\ControlPanel\Purchase\Application\DTO\GetPurchaseScalesRequest;
use App\ControlPanel\Purchase\Application\InputPorts\GetPurchaseScalesUseCaseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetPurchaseScalesController extends AbstractController
{
    private GetPurchaseScalesUseCaseInterface $getPurchaseScalesUseCase;

    public function __construct(GetPurchaseScalesUseCaseInterface $getPurchaseScalesUseCase)
    {
        $this->getPurchaseScalesUseCase = $getPurchaseScalesUseCase;
    }

    #[Route('/api/control-panel/purchases', name: 'get_purchase_scales', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        // Verificar que el usuario tiene rol ROOT
        if (!$this->isGranted('ROLE_ROOT')) {
            return new JsonResponse(
                ['error' => 'Access denied. ROOT role required.'],
                Response::HTTP_FORBIDDEN
            );
        }

        // Obtener el contenido JSON
        $content = $request->getContent();
        if (empty($content)) {
            $content = '{}';
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(
                ['error' => 'Invalid JSON format'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Crear el request DTO con filtros opcionales
        $getPurchaseScalesRequest = new GetPurchaseScalesRequest(
            $data['uuidPurchase'] ?? null,
            $data['uuidClient'] ?? null,
            $data['status'] ?? null
        );

        // Ejecutar el caso de uso
        $response = $this->getPurchaseScalesUseCase->execute($getPurchaseScalesRequest);

        return new JsonResponse($response->toArray(), Response::HTTP_OK);
    }
}
