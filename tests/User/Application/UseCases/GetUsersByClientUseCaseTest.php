<?php

namespace App\Tests\User\Application\UseCases;

use App\User\Application\UseCases\GetUsersByClientUseCase;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use PHPUnit\Framework\TestCase;
use App\Entity\Main\User;

class GetUsersByClientUseCaseTest extends TestCase
{
    private UserRepositoryInterface $userRepositoryMock;
    private GetUsersByClientUseCase $useCase;

    protected function setUp(): void
    {
        $this->userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->useCase = new GetUsersByClientUseCase($this->userRepositoryMock);
    }

    public function testGetUsersByClient_ReturnsUsers(): void
    {
        $clientUuid = 'client-uuid';
        $user = new User();
        $this->userRepositoryMock->expects($this->once())
            ->method('findByClientUuid')
            ->with($clientUuid)
            ->willReturn([$user]);

        $result = $this->useCase->getUsersByClient($clientUuid);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame($user, $result[0]);
    }

    public function testGetUsersByClient_ReturnsEmptyArray(): void
    {
        $clientUuid = 'client-uuid';
        $this->userRepositoryMock->expects($this->once())
            ->method('findByClientUuid')
            ->with($clientUuid)
            ->willReturn([]);

        $result = $this->useCase->getUsersByClient($clientUuid);

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }
}
