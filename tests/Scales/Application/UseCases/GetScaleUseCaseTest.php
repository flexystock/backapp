<?php

namespace App\Tests\Scales\Application\UseCases;

use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\DTO\GetScaleRequest;
use App\Scales\Application\UseCases\GetScaleUseCase;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class GetScaleUseCaseTest extends TestCase
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private GetScaleUseCase $useCase;

    protected function setUp(): void
    {
        $this->connectionManager = $this->createMock(ClientConnectionManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->useCase = new GetScaleUseCase(
            $this->connectionManager,
            $this->logger
        );
    }

    public function testGetScaleUseCaseCanBeInstantiated(): void
    {
        $this->assertInstanceOf(GetScaleUseCase::class, $this->useCase);
    }

    public function testGetScaleUseCaseHasCorrectDependencies(): void
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
