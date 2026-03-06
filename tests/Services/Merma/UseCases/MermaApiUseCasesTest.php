<?php

namespace App\Tests\Services\Merma\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Service\Merma\Application\DTO\ConfirmAnomalyRequest;
use App\Service\Merma\Application\DTO\DiscardAnomalyRequest;
use App\Service\Merma\Application\DTO\GetMermaConfigRequest;
use App\Service\Merma\Application\DTO\GetMermaMonthlyHistoryRequest;
use App\Service\Merma\Application\DTO\GetMermaSummaryRequest;
use App\Service\Merma\Application\UseCases\ConfirmAnomalyUseCase;
use App\Service\Merma\Application\UseCases\DiscardAnomalyUseCase;
use App\Service\Merma\Application\UseCases\GetMermaConfigUseCase;
use App\Service\Merma\Application\UseCases\GetMermaMonthlyHistoryUseCase;
use App\Service\Merma\Application\UseCases\GetMermaSummaryUseCase;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class MermaApiUseCasesTest extends TestCase
{
    private ClientRepositoryInterface $clientRepository;
    private ClientConnectionManager   $connectionManager;
    private LoggerInterface           $logger;

    protected function setUp(): void
    {
        $this->clientRepository  = $this->createMock(ClientRepositoryInterface::class);
        $this->connectionManager = $this->createMock(ClientConnectionManager::class);
        $this->logger            = $this->createMock(LoggerInterface::class);
    }

    // ── GetMermaSummaryUseCase ────────────────────────────────────────────────

    public function testGetMermaSummaryUseCaseCanBeInstantiated(): void
    {
        $useCase = new GetMermaSummaryUseCase(
            $this->clientRepository,
            $this->connectionManager,
            $this->logger,
        );
        $this->assertInstanceOf(GetMermaSummaryUseCase::class, $useCase);
    }

    public function testGetMermaSummaryThrowsExceptionWhenClientNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('CLIENT_NOT_FOUND');

        $this->clientRepository->method('findByUuid')->willReturn(null);

        $useCase = new GetMermaSummaryUseCase($this->clientRepository, $this->connectionManager, $this->logger);
        $useCase->execute(new GetMermaSummaryRequest('non-existent-uuid', 1, 1));
    }

    // ── GetMermaMonthlyHistoryUseCase ─────────────────────────────────────────

    public function testGetMermaMonthlyHistoryUseCaseCanBeInstantiated(): void
    {
        $useCase = new GetMermaMonthlyHistoryUseCase(
            $this->clientRepository,
            $this->connectionManager,
            $this->logger,
        );
        $this->assertInstanceOf(GetMermaMonthlyHistoryUseCase::class, $useCase);
    }

    public function testGetMermaMonthlyHistoryThrowsExceptionWhenClientNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('CLIENT_NOT_FOUND');

        $this->clientRepository->method('findByUuid')->willReturn(null);

        $useCase = new GetMermaMonthlyHistoryUseCase($this->clientRepository, $this->connectionManager, $this->logger);
        $useCase->execute(new GetMermaMonthlyHistoryRequest('non-existent-uuid', 1, 1));
    }

    // ── ConfirmAnomalyUseCase ─────────────────────────────────────────────────

    public function testConfirmAnomalyUseCaseCanBeInstantiated(): void
    {
        $useCase = new ConfirmAnomalyUseCase(
            $this->clientRepository,
            $this->connectionManager,
            $this->logger,
        );
        $this->assertInstanceOf(ConfirmAnomalyUseCase::class, $useCase);
    }

    public function testConfirmAnomalyThrowsExceptionWhenClientNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('CLIENT_NOT_FOUND');

        $this->clientRepository->method('findByUuid')->willReturn(null);

        $useCase = new ConfirmAnomalyUseCase($this->clientRepository, $this->connectionManager, $this->logger);
        $useCase->execute(new ConfirmAnomalyRequest('non-existent-uuid', 1));
    }

    // ── DiscardAnomalyUseCase ─────────────────────────────────────────────────

    public function testDiscardAnomalyUseCaseCanBeInstantiated(): void
    {
        $useCase = new DiscardAnomalyUseCase(
            $this->clientRepository,
            $this->connectionManager,
            $this->logger,
        );
        $this->assertInstanceOf(DiscardAnomalyUseCase::class, $useCase);
    }

    public function testDiscardAnomalyThrowsExceptionWhenClientNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('CLIENT_NOT_FOUND');

        $this->clientRepository->method('findByUuid')->willReturn(null);

        $useCase = new DiscardAnomalyUseCase($this->clientRepository, $this->connectionManager, $this->logger);
        $useCase->execute(new DiscardAnomalyRequest('non-existent-uuid', 1));
    }

    // ── GetMermaConfigUseCase ─────────────────────────────────────────────────

    public function testGetMermaConfigUseCaseCanBeInstantiated(): void
    {
        $useCase = new GetMermaConfigUseCase(
            $this->clientRepository,
            $this->connectionManager,
            $this->logger,
        );
        $this->assertInstanceOf(GetMermaConfigUseCase::class, $useCase);
    }

    public function testGetMermaConfigThrowsExceptionWhenClientNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('CLIENT_NOT_FOUND');

        $this->clientRepository->method('findByUuid')->willReturn(null);

        $useCase = new GetMermaConfigUseCase($this->clientRepository, $this->connectionManager, $this->logger);
        $useCase->execute(new GetMermaConfigRequest('non-existent-uuid', 1));
    }

    // ── UpdateMermaConfigUseCase ──────────────────────────────────────────────

    public function testUpdateMermaConfigUseCaseCanBeInstantiated(): void
    {
        $useCase = new \App\Service\Merma\Application\UseCases\UpdateMermaConfigUseCase(
            $this->clientRepository,
            $this->connectionManager,
            $this->logger,
        );
        $this->assertInstanceOf(\App\Service\Merma\Application\UseCases\UpdateMermaConfigUseCase::class, $useCase);
    }

    public function testUpdateMermaConfigThrowsExceptionWhenClientNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('CLIENT_NOT_FOUND');

        $this->clientRepository->method('findByUuid')->willReturn(null);

        $useCase = new \App\Service\Merma\Application\UseCases\UpdateMermaConfigUseCase($this->clientRepository, $this->connectionManager, $this->logger);
        $useCase->execute(new \App\Service\Merma\Application\DTO\UpdateMermaConfigRequest('non-existent-uuid', 1, 80, '09:00', '23:59', 0.200, true));
    }
}
