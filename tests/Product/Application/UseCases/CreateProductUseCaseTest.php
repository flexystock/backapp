<?php

namespace App\Tests\Product\Application\UseCases;

use App\Product\Application\DTO\CreateProductRequest;
use App\Product\Application\DTO\CreateProductResponse;
use App\Product\Application\OutputPorts\Repositories\ProductRepositoryInterface;
use App\Product\Application\UseCases\CreateProductUseCase;
use PHPUnit\Framework\TestCase;

class CreateProductUseCaseTest extends TestCase
{
    public function testExecuteCreatesProductSuccessfully(): void
    {
        $productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);

        // Esperamos que el repositorio llame a su método para guardar el producto
        $productRepositoryMock->expects($this->once())
            ->method('save')
            ->willReturn('generated-product-uuid'); // O lo que tu save retorne.

        $useCase = new CreateProductUseCase($productRepositoryMock);

        $request = new CreateProductRequest('c014a415-4113-49e5-80cb-cc3158c15236', 'Producto test', 'Descripción');
        $response = $useCase->execute($request);

        $this->assertInstanceOf(CreateProductResponse::class, $response);
        $this->assertEquals('generated-product-uuid', $response->getUuid());
        $this->assertNull($response->getError());
    }

    public function testExecuteFailsIfNoUuidClientProvided(): void
    {
        $productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);
        // Esta vez no esperamos que se llame a save si los datos son inválidos
        $productRepositoryMock->expects($this->never())->method('save');

        $useCase = new CreateProductUseCase($productRepositoryMock);

        $request = new CreateProductRequest('', 'Producto test', 'Descripción');
        $response = $useCase->execute($request);

        $this->assertInstanceOf(CreateProductResponse::class, $response);
        $this->assertNotNull($response->getError());
        $this->assertEquals('Invalid UUID client', $response->getError());
    }
}
