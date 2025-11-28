<?php

namespace App\Tests\Report\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Infrastructure\Services\ClientConnectionManager;
use App\Report\Application\DTO\GenerateReportNowRequest;
use App\Report\Application\UseCases\GenerateReportNowUseCase;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

class GenerateReportNowUseCaseTest extends TestCase
{
    private ClientRepositoryInterface $clientRepository;
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private MailerInterface $mailer;
    private Environment $twig;
    private GenerateReportNowUseCase $useCase;

    protected function setUp(): void
    {
        $this->clientRepository = $this->createMock(ClientRepositoryInterface::class);
        $this->connectionManager = $this->createMock(ClientConnectionManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->mailer = $this->createMock(MailerInterface::class);
        $this->twig = $this->createMock(Environment::class);

        $this->useCase = new GenerateReportNowUseCase(
            $this->clientRepository,
            $this->connectionManager,
            $this->logger,
            $this->mailer,
            $this->twig
        );
    }

    public function testGenerateReportNowUseCaseCanBeInstantiated(): void
    {
        $this->assertInstanceOf(GenerateReportNowUseCase::class, $this->useCase);
    }

    public function testGenerateReportNowUseCaseHasCorrectDependencies(): void
    {
        $reflection = new \ReflectionClass($this->useCase);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertCount(5, $constructor->getParameters());

        $params = $constructor->getParameters();
        $this->assertEquals('clientRepository', $params[0]->getName());
        $this->assertEquals('connectionManager', $params[1]->getName());
        $this->assertEquals('logger', $params[2]->getName());
        $this->assertEquals('mailer', $params[3]->getName());
        $this->assertEquals('twig', $params[4]->getName());
    }

    public function testExecuteThrowsExceptionWhenClientNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('CLIENT_NOT_FOUND');

        $request = new GenerateReportNowRequest(
            'non-existent-uuid',
            'Test Report',
            'csv',
            'all',
            'test@example.com'
        );

        $this->clientRepository
            ->method('findByUuid')
            ->with('non-existent-uuid')
            ->willReturn(null);

        $this->useCase->execute($request);
    }
}
