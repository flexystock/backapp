<?php

namespace App\Tests\Scales\Application\UseCases;

use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\DTO\AssignScaleToProductRequest;
use App\Scales\Application\UseCases\AssignScaleToProductUseCase;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AssignScaleToProductUseCaseTest extends TestCase
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private AssignScaleToProductUseCase $useCase;

    protected function setUp(): void
    {
        $this->connectionManager = $this->createMock(ClientConnectionManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->useCase = new AssignScaleToProductUseCase(
            $this->connectionManager,
            $this->logger
        );
    }

    public function testAssignScaleToProductUseCaseCanBeInstantiated(): void
    {
        $this->assertInstanceOf(AssignScaleToProductUseCase::class, $this->useCase);
    }

    public function testAssignScaleToProductUseCaseHasCorrectDependencies(): void
    {
        $reflection = new \ReflectionClass($this->useCase);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertCount(2, $constructor->getParameters());

        $params = $constructor->getParameters();
        $this->assertEquals('connectionManager', $params[0]->getName());
        $this->assertEquals('logger', $params[1]->getName());
    }
}
