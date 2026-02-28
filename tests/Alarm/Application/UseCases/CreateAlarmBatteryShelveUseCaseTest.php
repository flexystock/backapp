<?php

namespace App\Tests\Alarm\Application\UseCases;

use App\Alarm\Application\DTO\CreateAlarmBatteryShelveRequest;
use App\Alarm\Application\UseCases\CreateAlarmBatteryShelveUseCase;
use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Entity\Client\ClientConfig;
use App\Entity\Main\Client;
use App\Infrastructure\Services\ClientConnectionManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CreateAlarmBatteryShelveUseCaseTest extends TestCase
{
    private ClientRepositoryInterface $clientRepository;
    private ClientConnectionManager $connectionManager;
    private LoggerInterface $logger;
    private CreateAlarmBatteryShelveUseCase $useCase;

    protected function setUp(): void
    {
        $this->clientRepository = $this->createMock(ClientRepositoryInterface::class);
        $this->connectionManager = $this->createMock(ClientConnectionManager::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->useCase = new CreateAlarmBatteryShelveUseCase(
            $this->clientRepository,
            $this->connectionManager,
            $this->logger
        );
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(CreateAlarmBatteryShelveUseCase::class, $this->useCase);
    }

    public function testExecuteThrowsExceptionWhenClientNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('CLIENT_NOT_FOUND');

        $this->clientRepository
            ->method('findByUuid')
            ->with('non-existent-uuid')
            ->willReturn(null);

        $request = new CreateAlarmBatteryShelveRequest('non-existent-uuid', 1);

        $this->useCase->execute($request);
    }

    public function testExecuteCreatesNewConfigWhenNoneExists(): void
    {
        $uuidClient = 'a1b2c3d4-e5f6-7890-abcd-ef1234567890';

        $client = $this->createMock(Client::class);
        $client->method('getUuidClient')->willReturn($uuidClient);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(EntityRepository::class);
        $repository->method('findOneBy')->with([])->willReturn(null);
        $entityManager->method('getRepository')->willReturn($repository);
        $entityManager->expects($this->once())->method('persist');
        $entityManager->expects($this->once())->method('flush');

        $this->clientRepository->method('findByUuid')->willReturn($client);
        $this->connectionManager->method('getEntityManager')->with($uuidClient)->willReturn($entityManager);

        $this->logger->expects($this->once())->method('info')->with(
            'Created client config for battery shelve alarm',
            ['uuidClient' => $uuidClient]
        );

        $request = new CreateAlarmBatteryShelveRequest($uuidClient, 1);

        $response = $this->useCase->execute($request);

        $this->assertTrue($response->isCheckBatteryShelveEnabled());
    }

    public function testExecuteUpdatesExistingConfig(): void
    {
        $uuidClient = 'a1b2c3d4-e5f6-7890-abcd-ef1234567890';

        $client = $this->createMock(Client::class);
        $client->method('getUuidClient')->willReturn($uuidClient);

        $existingConfig = new ClientConfig();
        $existingConfig->setUuidUserCreation('original-user');
        $existingConfig->setDatehourCreation(new \DateTimeImmutable('-1 day'));

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(EntityRepository::class);
        $repository->method('findOneBy')->with([])->willReturn($existingConfig);
        $entityManager->method('getRepository')->willReturn($repository);
        $entityManager->expects($this->once())->method('persist');
        $entityManager->expects($this->once())->method('flush');

        $this->clientRepository->method('findByUuid')->willReturn($client);
        $this->connectionManager->method('getEntityManager')->with($uuidClient)->willReturn($entityManager);

        $this->logger->expects($this->never())->method('info');

        $request = new CreateAlarmBatteryShelveRequest($uuidClient, 1);
        $request->setUuidUser('test-user');

        $response = $this->useCase->execute($request);

        $this->assertTrue($response->isCheckBatteryShelveEnabled());
        $this->assertEquals('test-user', $existingConfig->getUuidUserModification());
    }

    public function testExecuteWithBatteryShelveDisabled(): void
    {
        $uuidClient = 'a1b2c3d4-e5f6-7890-abcd-ef1234567890';

        $client = $this->createMock(Client::class);
        $client->method('getUuidClient')->willReturn($uuidClient);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(EntityRepository::class);
        $repository->method('findOneBy')->with([])->willReturn(null);
        $entityManager->method('getRepository')->willReturn($repository);
        $entityManager->method('persist');
        $entityManager->method('flush');

        $this->clientRepository->method('findByUuid')->willReturn($client);
        $this->connectionManager->method('getEntityManager')->willReturn($entityManager);

        $request = new CreateAlarmBatteryShelveRequest($uuidClient, 0);

        $response = $this->useCase->execute($request);

        $this->assertFalse($response->isCheckBatteryShelveEnabled());
    }

    public function testExecuteUsesDefaultTimestampAndSystemUserWhenNotProvided(): void
    {
        $uuidClient = 'a1b2c3d4-e5f6-7890-abcd-ef1234567890';

        $client = $this->createMock(Client::class);
        $client->method('getUuidClient')->willReturn($uuidClient);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(EntityRepository::class);
        $repository->method('findOneBy')->with([])->willReturn(null);
        $entityManager->method('getRepository')->willReturn($repository);

        $persistedConfig = null;
        $entityManager->method('persist')->willReturnCallback(function (ClientConfig $config) use (&$persistedConfig) {
            $persistedConfig = $config;
        });
        $entityManager->method('flush');

        $this->clientRepository->method('findByUuid')->willReturn($client);
        $this->connectionManager->method('getEntityManager')->willReturn($entityManager);

        $request = new CreateAlarmBatteryShelveRequest($uuidClient, 1);
        // uuidUser and timestamp are not set — should default to 'system' and now

        $this->useCase->execute($request);

        $this->assertNotNull($persistedConfig);
        $this->assertEquals('system', $persistedConfig->getUuidUserCreation());
        $this->assertInstanceOf(\DateTimeInterface::class, $persistedConfig->getDatehourCreation());
    }
}
