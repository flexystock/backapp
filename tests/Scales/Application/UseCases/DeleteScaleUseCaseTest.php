<?php

namespace App\Tests\Scales\Application\UseCases;

use App\Entity\Main\User;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Scales\Application\DTO\DeleteScaleRequest;
use App\Scales\Application\UseCases\DeleteScaleUseCase;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DeleteScaleUseCaseTest extends TestCase
{
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private DeleteScaleUseCase $useCase;

    protected function setUp(): void
    {
        $this->connectionManager = $this->createMock(ClientConnectionManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->useCase = new DeleteScaleUseCase(
            $this->connectionManager,
            $this->logger
        );
    }

    public function testDeleteScaleUseCaseCanBeInstantiated(): void
    {
        $this->assertInstanceOf(DeleteScaleUseCase::class, $this->useCase);
    }

    public function testDeleteScaleUseCaseHasCorrectDependencies(): void
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
        $request = new DeleteScaleRequest('client-uuid', 'scale-uuid');
        
        $this->assertInstanceOf(DeleteScaleRequest::class, $request);
        $this->assertEquals('client-uuid', $request->getUuidClient());
        $this->assertEquals('scale-uuid', $request->getUuidScale());
    }

    public function testExecuteRequiresUserParameter(): void
    {
        $reflection = new \ReflectionMethod($this->useCase, 'execute');
        $parameters = $reflection->getParameters();

        $this->assertCount(2, $parameters);
        $this->assertEquals('request', $parameters[0]->getName());
        $this->assertEquals('user', $parameters[1]->getName());
    }
}
