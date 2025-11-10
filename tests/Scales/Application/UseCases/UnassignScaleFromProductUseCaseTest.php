<?php

namespace App\Tests\Scales\Application\UseCases;

use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\OutputPorts\PoolScalesRepositoryInterface;
use App\Scales\Application\OutputPorts\ScalesRepositoryInterface;
use App\Scales\Application\UseCases\UnassignScaleFromProductUseCase;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class UnassignScaleFromProductUseCaseTest extends TestCase
{
    private ClientConnectionManager $clientConnectionManager;
    private PoolScalesRepositoryInterface $poolScalesRepository;
    private ScalesRepositoryInterface $scalesRepository;
    private LoggerInterface $logger;
    private UnassignScaleFromProductUseCase $useCase;

    protected function setUp(): void
    {
        $this->clientConnectionManager = $this->createMock(ClientConnectionManager::class);
        $this->poolScalesRepository = $this->createMock(PoolScalesRepositoryInterface::class);
        $this->scalesRepository = $this->createMock(ScalesRepositoryInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->useCase = new UnassignScaleFromProductUseCase(
            $this->clientConnectionManager,
            $this->poolScalesRepository,
            $this->scalesRepository,
            $this->logger
        );
    }

    public function testUnassignScaleFromProductUseCaseCanBeInstantiated(): void
    {
        $this->assertInstanceOf(UnassignScaleFromProductUseCase::class, $this->useCase);
    }

    public function testUnassignScaleFromProductUseCaseHasCorrectDependencies(): void
    {
        $reflection = new \ReflectionClass($this->useCase);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertCount(4, $constructor->getParameters());

        $params = $constructor->getParameters();
        $this->assertEquals('clientConnectionManager', $params[0]->getName());
        $this->assertEquals('poolScalesRepository', $params[1]->getName());
        $this->assertEquals('scalesRepository', $params[2]->getName());
        $this->assertEquals('logger', $params[3]->getName());
    }
}
