<?php

namespace App\Tests\Scales\Application\UseCases;

use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\DTO\UpdateScaleRequest;
use App\Scales\Application\UseCases\UpdateScaleUseCase;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class UpdateScaleUseCaseTest extends TestCase
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private UpdateScaleUseCase $useCase;

    protected function setUp(): void
    {
        $this->connectionManager = $this->createMock(ClientConnectionManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->useCase = new UpdateScaleUseCase(
            $this->connectionManager,
            $this->logger
        );
    }

    public function testUpdateScaleUseCaseCanBeInstantiated(): void
    {
        $this->assertInstanceOf(UpdateScaleUseCase::class, $this->useCase);
    }

    public function testUpdateScaleUseCaseHasCorrectDependencies(): void
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
