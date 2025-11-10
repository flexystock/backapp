<?php

namespace App\Tests\Scales\Application\UseCases;

use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\DTO\RegisterScalesRequest;
use App\Scales\Application\OutputPorts\ScalesRepositoryInterface;
use App\Scales\Application\UseCases\RegisterScalesUseCase;
use PHPUnit\Framework\TestCase;

class RegisterScalesUseCaseTest extends TestCase
{
    private ScalesRepositoryInterface $scalesRepository;
    private ClientConnectionManager $connectionManager;
    private RegisterScalesUseCase $useCase;

    protected function setUp(): void
    {
        $this->scalesRepository = $this->createMock(ScalesRepositoryInterface::class);
        $this->connectionManager = $this->createMock(ClientConnectionManager::class);

        $this->useCase = new RegisterScalesUseCase(
            $this->scalesRepository,
            $this->connectionManager
        );
    }

    public function testRegisterScalesUseCaseCanBeInstantiated(): void
    {
        $this->assertInstanceOf(RegisterScalesUseCase::class, $this->useCase);
    }

    public function testRegisterScalesUseCaseHasCorrectDependencies(): void
    {
        $reflection = new \ReflectionClass($this->useCase);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertCount(2, $constructor->getParameters());

        $params = $constructor->getParameters();
        $this->assertEquals('scalesRepository', $params[0]->getName());
        $this->assertEquals('connectionManager', $params[1]->getName());
    }
}
