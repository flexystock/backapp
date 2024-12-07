<?php

namespace App\Product\Infrastructure\InputAdapters;

use App\Product\Application\InputPorts\GetProductInputPort;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ProductController
{
    private GetProductInputPort $getProductUseCase;

    public function __construct(GetProductInputPort $getProductUseCase)
    {
        $this->getProductUseCase = $getProductUseCase;
    }

    #[Route('/api/product', name: 'get_product', methods: ['POST'])]
    public function getProduct(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $uuid = $data['uuid'] ?? null;

        $dto = $this->getProductUseCase->execute($uuid);

        return new JsonResponse([
            'uuid' => $dto->uuid,
            'name' => $dto->name,
        ]);
    }
}
