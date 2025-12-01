<?php

namespace App\Tests\Report\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Report\Application\DTO\DeleteReportRequest;
use App\Report\Application\UseCases\DeleteReportUseCase;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DeleteReportUseCaseTest extends TestCase
{
    private ClientRepositoryInterface $clientRepository;
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private DeleteReportUseCase $useCase;

    protected function setUp(): void
    {
        $this->clientRepository = $this->createMock(ClientRepositoryInterface::class);
        $this->connectionManager = $this->createMock(ClientConnectionManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->useCase = new DeleteReportUseCase(
            $this->clientRepository,
            $this->connectionManager,
            $this->logger
        );
    }

    public function testDeleteReportUseCaseCanBeInstantiated(): void
    {
        $this->assertInstanceOf(DeleteReportUseCase::class, $this->useCase);
    }

    public function testExecuteThrowsExceptionWhenClientNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('CLIENT_NOT_FOUND');

        $request = new DeleteReportRequest(
            'non-existent-uuid',
            1
        );

        $this->clientRepository
            ->method('findByUuid')
            ->with('non-existent-uuid')
            ->willReturn(null);

        $this->useCase->execute($request);
    }
}
