<?php

namespace App\Product\Application\UseCases;

use App\Product\Application\DTO\ProductDTO;
use App\Product\Application\InputPorts\GetProductInputPort;
use App\Product\Application\OutputPorts\Repositories\ProductRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetProductUseCase implements GetProductInputPort
{
    private ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function execute(string $productUuid): ProductDTO
    {
        $product = $this->productRepository->findByUuid($productUuid);
        if (!$product) {
            throw new NotFoundHttpException('Product not found.');
        }

        return new ProductDTO(
            $product->getUuid(),
            $product->getName()
            // otros campos...
        );
    }
}
