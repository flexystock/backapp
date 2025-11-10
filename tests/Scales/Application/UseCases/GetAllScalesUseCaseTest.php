<?php

namespace App\Tests\Scales\Application\UseCases;

use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\DTO\GetAllScalesRequest;
use App\Scales\Application\UseCases\GetAllScalesUseCase;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class GetAllScalesUseCaseTest extends TestCase
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private GetAllScalesUseCase $useCase;

    protected function setUp(): void
    {
        $this->connectionManager = $this->createMock(ClientConnectionManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->useCase = new GetAllScalesUseCase(
            $this->connectionManager,
            $this->logger
        );
    }

    public function testGetAllScalesUseCaseCanBeInstantiated(): void
    {
        $this->assertInstanceOf(GetAllScalesUseCase::class, $this->useCase);
    }

    public function testGetAllScalesUseCaseHasCorrectDependencies(): void
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
