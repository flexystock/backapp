<?php

namespace App\Tests\Product\Application\UseCases;
use PHPUnit\Framework\TestCase;
class DeleteProductUseCaseTest extends TestCase
{
    public function testExecuteDeletesProduct(): void
    {
        $productRepositoryMock = $this->createMock(ProductRepositoryInterface::class);
        $productRepositoryMock->expects($this->once())
            ->method('deleteByUuid')
            ->with('some-uuid-product')
            ->willReturn(true);

        $useCase = new DeleteProductUseCase($productRepositoryMock);

        $request = new DeleteProductRequest('c014a415-4113-49e5-80cb-cc3158c15236', 'some-uuid-product');
        $response = $useCase->execute($request);

        $this->assertTrue($response->isSuccess());
        $this->assertNull($response->getError());
    }

}