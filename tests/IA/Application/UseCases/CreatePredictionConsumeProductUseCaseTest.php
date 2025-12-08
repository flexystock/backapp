<?php

namespace App\Tests\IA\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\IA\Application\DTO\CreatePredictionConsumeProductRequest;
use App\IA\Application\UseCases\CreatePredictionConsumeProductUseCase;
use App\Infrastructure\Services\ClientConnectionManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CreatePredictionConsumeProductUseCaseTest extends TestCase
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private ClientRepositoryInterface $clientRepository;
    private CreatePredictionConsumeProductUseCase $useCase;

    protected function setUp(): void
    {
        $this->connectionManager = $this->createMock(ClientConnectionManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->clientRepository = $this->createMock(ClientRepositoryInterface::class);

        $this->useCase = new CreatePredictionConsumeProductUseCase(
            $this->connectionManager,
            $this->logger,
            $this->clientRepository
        );
    }

    public function testCreatePredictionConsumeProductUseCaseCanBeInstantiated(): void
    {
        $this->assertInstanceOf(CreatePredictionConsumeProductUseCase::class, $this->useCase);
    }

    public function testCreatePredictionConsumeProductUseCaseHasCorrectDependencies(): void
    {
        $reflection = new \ReflectionClass($this->useCase);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertCount(3, $constructor->getParameters());

        $params = $constructor->getParameters();
        $this->assertEquals('connectionManager', $params[0]->getName());
        $this->assertEquals('logger', $params[1]->getName());
        $this->assertEquals('clientRepository', $params[2]->getName());
    }

    public function testExecuteReturnsErrorWhenClientNotFound(): void
    {
        $request = new CreatePredictionConsumeProductRequest(
            'non-existent-uuid',
            1
        );

        $this->clientRepository
            ->method('findByUuid')
            ->with('non-existent-uuid')
            ->willReturn(null);

        $response = $this->useCase->execute($request);

        $this->assertNull($response->getPrediction());
        $this->assertEquals('CLIENT_NOT_FOUND', $response->getError());
        $this->assertEquals(404, $response->getStatusCode());
    }
}
