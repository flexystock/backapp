<?php

namespace App\Tests\Scales\Application\UseCases;

use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\DTO\GetInfoScalesToDashboardMainRequest;
use App\Scales\Application\UseCases\GetInfoScalesToDashboardMainUseCase;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class GetInfoScalesToDashboardMainUseCaseTest extends TestCase
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private GetInfoScalesToDashboardMainUseCase $useCase;

    protected function setUp(): void
    {
        $this->connectionManager = $this->createMock(ClientConnectionManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->useCase = new GetInfoScalesToDashboardMainUseCase(
            $this->connectionManager,
            $this->logger
        );
    }

    public function testGetInfoScalesToDashboardMainUseCaseCanBeInstantiated(): void
    {
        $this->assertInstanceOf(GetInfoScalesToDashboardMainUseCase::class, $this->useCase);
    }

    public function testGetInfoScalesToDashboardMainUseCaseHasCorrectDependencies(): void
    {
        $reflection = new \ReflectionClass($this->useCase);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertCount(2, $constructor->getParameters());

        $params = $constructor->getParameters();
        $this->assertEquals('connectionManager', $params[0]->getName());
        $this->assertEquals('logger', $params[1]->getName());
    }

    public function testExecuteMethodExists(): void
    {
        $this->assertTrue(method_exists($this->useCase, 'execute'));
    }

    public function testRequestDTOCanBeCreated(): void
    {
        $request = new GetInfoScalesToDashboardMainRequest('client-uuid');
        
        $this->assertInstanceOf(GetInfoScalesToDashboardMainRequest::class, $request);
        $this->assertEquals('client-uuid', $request->getUuidClient());
    }
}
