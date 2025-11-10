<?php

namespace App\Tests\Product\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Product\Application\DTO\CreateProductRequest;
use App\Product\Application\OutputPorts\Repositories\ProductRepositoryInterface;
use App\Product\Application\UseCases\CreateProductUseCase;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CreateProductUseCaseTest extends TestCase
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private ProductRepositoryInterface $productRepository;
    private ClientRepositoryInterface $clientRepository;
    private UserRepositoryInterface $userRepository;
    private CreateProductUseCase $useCase;

    protected function setUp(): void
    {
        // Crear mocks de todas las dependencias
        $this->connectionManager = $this->createMock(ClientConnectionManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->clientRepository = $this->createMock(ClientRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);

        $this->useCase = new CreateProductUseCase(
            $this->connectionManager,
            $this->logger,
            $this->productRepository,
            $this->clientRepository,
            $this->userRepository
        );
    }

    public function testCreateProductUseCaseCanBeInstantiated(): void
    {
        $this->assertInstanceOf(CreateProductUseCase::class, $this->useCase);
    }

    public function testCreateProductUseCaseHasCorrectDependencies(): void
    {
        $reflection = new \ReflectionClass($this->useCase);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertCount(5, $constructor->getParameters());

        $params = $constructor->getParameters();
        $this->assertEquals('connectionManager', $params[0]->getName());
        $this->assertEquals('logger', $params[1]->getName());
        $this->assertEquals('productRepository', $params[2]->getName());
        $this->assertEquals('clientRepository', $params[3]->getName());
        $this->assertEquals('userRepository', $params[4]->getName());
    }

    public function testExecuteThrowsExceptionWhenClientNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('CLIENT_NOT_FOUND');

        $request = new CreateProductRequest(
            'non-existent-uuid',
            'Producto test'
        );

        // Mock: cliente NO existe
        $this->clientRepository
            ->method('findByUuid')
            ->with('non-existent-uuid')
            ->willReturn(null);

        // Ejecutar - debe lanzar excepción
        $this->useCase->execute($request);
    }
}