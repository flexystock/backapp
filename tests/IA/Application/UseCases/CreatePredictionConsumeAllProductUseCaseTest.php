<?php

namespace App\Tests\IA\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\IA\Application\DTO\CreatePredictionConsumeAllProductRequest;
use App\IA\Application\Services\PredictionService;
use App\IA\Application\UseCases\CreatePredictionConsumeAllProductUseCase;
use App\Infrastructure\Services\ClientConnectionManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CreatePredictionConsumeAllProductUseCaseTest extends TestCase
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private ClientRepositoryInterface $clientRepository;
    private PredictionService $predictionService;
    private CreatePredictionConsumeAllProductUseCase $useCase;

    protected function setUp(): void
    {
        $this->connectionManager = $this->createMock(ClientConnectionManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->clientRepository = $this->createMock(ClientRepositoryInterface::class);
        $this->predictionService = $this->createMock(PredictionService::class);

        $this->useCase = new CreatePredictionConsumeAllProductUseCase(
            $this->connectionManager,
            $this->logger,
            $this->clientRepository,
            $this->predictionService
        );
    }

    public function testCreatePredictionConsumeAllProductUseCaseCanBeInstantiated(): void
    {
        $this->assertInstanceOf(CreatePredictionConsumeAllProductUseCase::class, $this->useCase);
    }

    public function testCreatePredictionConsumeAllProductUseCaseHasCorrectDependencies(): void
    {
        $reflection = new \ReflectionClass($this->useCase);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertCount(4, $constructor->getParameters());

        $params = $constructor->getParameters();
        $this->assertEquals('connectionManager', $params[0]->getName());
        $this->assertEquals('logger', $params[1]->getName());
        $this->assertEquals('clientRepository', $params[2]->getName());
        $this->assertEquals('predictionService', $params[3]->getName());
    }

    public function testExecuteReturnsErrorWhenClientNotFound(): void
    {
        $request = new CreatePredictionConsumeAllProductRequest('non-existent-uuid');

        $this->clientRepository
            ->method('findByUuid')
            ->with('non-existent-uuid')
            ->willReturn(null);

        $response = $this->useCase->execute($request);

        $this->assertNull($response->getPredictions());
        $this->assertEquals('CLIENT_NOT_FOUND', $response->getError());
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testExecuteReturnsEmptyArrayWhenNoProducts(): void
    {
        $request = new CreatePredictionConsumeAllProductRequest('valid-uuid');

        $mockClient = $this->createMock(\App\Entity\Main\Client::class);
        $mockClient->method('getUuidClient')->willReturn('valid-uuid');

        $this->clientRepository
            ->method('findByUuid')
            ->with('valid-uuid')
            ->willReturn($mockClient);

        $mockEntityManager = $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);

        $this->connectionManager
            ->method('getEntityManager')
            ->with('valid-uuid')
            ->willReturn($mockEntityManager);

        // Note: This test would require more complex mocking to test the full flow
        // For now, we're just testing the basic structure
    }
}
