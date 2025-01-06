<?php

// src/Product/Infrastructure/InputAdapters/ProductController.php

namespace App\Product\Infrastructure\InputAdapters;

use App\Product\Application\DTO\CreateProductRequest;
use App\Product\Application\DTO\DeleteProductRequest;
use App\Product\Application\DTO\GetAllProductsRequest;
use App\Product\Application\DTO\GetProductRequest;
use App\Product\Application\DTO\UpdateProductRequest;
use App\Product\Application\InputPorts\CreateProductUseCaseInterface;
use App\Product\Application\InputPorts\DeleteProductUseCaseInterface;
use App\Product\Application\InputPorts\GetAllProductsUseCaseInterface;
use App\Product\Application\InputPorts\GetProductUseCaseInterface;
use App\Product\Application\InputPorts\UpdateProductUseCaseInterface;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private GetProductUseCaseInterface $getProductUseCase;
    private GetAllProductsUseCaseInterface $getAllProductsUseCase;
    private CreateProductUseCaseInterface $createProductUseCase;
    private LoggerInterface $logger;
    private DeleteProductUseCaseInterface $deleteProductUseCase;
    private UpdateProductUseCaseInterface $updateProductUseCase;

    public function __construct(GetProductUseCaseInterface $getProductUseCase, LoggerInterface $logger,
        GetAllProductsUseCaseInterface $getAllProductsUseCase, CreateProductUseCaseInterface $createProductUseCase,
        DeleteProductUseCaseInterface $deleteProductUseCase, UpdateProductUseCaseInterface $updateProductUseCase)
    {
        $this->getProductUseCase = $getProductUseCase;
        $this->getAllProductsUseCase = $getAllProductsUseCase;
        $this->logger = $logger;
        $this->createProductUseCase = $createProductUseCase;
        $this->deleteProductUseCase = $deleteProductUseCase;
        $this->updateProductUseCase = $updateProductUseCase;
    }








}
