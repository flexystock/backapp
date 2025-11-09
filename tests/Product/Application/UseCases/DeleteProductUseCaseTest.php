<?php

namespace App\Tests\Product\Application\UseCases;

use App\Entity\Main\User;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Product\Application\DTO\DeleteProductRequest;
use App\Product\Application\UseCases\DeleteProductUseCase;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DeleteProductUseCaseTest extends TestCase
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private DeleteProductUseCase $deleteProductUseCase;

    protected function setUp(): void
    {
        $this->connectionManager = $this->createMock(ClientConnectionManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->deleteProductUseCase = new DeleteProductUseCase(
            $this->connectionManager,
            $this->logger
        );
    }

    public function testDeleteProductUseCaseCanBeInstantiated(): void
    {
        $this->assertInstanceOf(DeleteProductUseCase::class, $this->deleteProductUseCase);
    }

    public function testDeleteProductUseCaseHasCorrectDependencies(): void
    {
        // Verificar que el use case fue construido correctamente
        $reflection = new \ReflectionClass($this->deleteProductUseCase);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertCount(2, $constructor->getParameters());

        $params = $constructor->getParameters();
        $this->assertEquals('connectionManager', $params[0]->getName());
        $this->assertEquals('logger', $params[1]->getName());
    }

    public function testDeleteProductThrowsExceptionWhenClientNotProvided(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('CLIENT_NOT_FOUND');

        // Crear un request sin cliente (string vacío)
        $request = new DeleteProductRequest('', 'product-uuid');
        $user = $this->createMock(User::class);

        $this->deleteProductUseCase->execute($request, $user);
    }

    public function testDeleteProductThrowsExceptionWhenProductNotProvided(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('PRODUCT_NOT_FOUND');

        // Crear un request sin producto (string vacío)
        $request = new DeleteProductRequest('client-uuid', '');
        $user = $this->createMock(User::class);

        $this->deleteProductUseCase->execute($request, $user);
    }
}