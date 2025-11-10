<?php

namespace App\Tests\User\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use App\Subscription\Application\OutputPorts\SubscriptionRepositoryInterface;
use PHPUnit\Framework\TestCase;
use App\User\Application\UseCases\GetUserClientsUseCase;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use App\Client\Application\DTO\ClientDTOCollection;
use App\Entity\Main\User;
use App\Entity\Main\Client;

class GetUserClientsUseCaseTest extends TestCase
{
    private UserRepositoryInterface $userRepositoryMock;
    private ClientRepositoryInterface $clientRepositoryMock;
    private SubscriptionRepositoryInterface $subscriptionRepositoryMock;
    private GetUserClientsUseCase $getUserClientsUseCase;

    protected function setUp(): void
    {
        // Crear mocks de los repositorios
        $this->userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->clientRepositoryMock = $this->createMock(ClientRepositoryInterface::class);
        $this->subscriptionRepositoryMock = $this->createMock(SubscriptionRepositoryInterface::class);

        // Crear la instancia del caso de uso con los repositorios mockeados
        $this->getUserClientsUseCase = new GetUserClientsUseCase(
            $this->userRepositoryMock,
            $this->clientRepositoryMock,
            $this->subscriptionRepositoryMock
        );
    }

    public function testGetUserClientsUseCaseCanBeInstantiated(): void
    {
        $this->assertInstanceOf(GetUserClientsUseCase::class, $this->getUserClientsUseCase);
    }

    public function testGetUserClientsUseCaseHasCorrectDependencies(): void
    {
        $reflection = new \ReflectionClass($this->getUserClientsUseCase);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);
        $this->assertCount(3, $constructor->getParameters());

        $params = $constructor->getParameters();
        $this->assertEquals('userRepository', $params[0]->getName());
        $this->assertEquals('clientRepository', $params[1]->getName());
        $this->assertEquals('subscriptionRepository', $params[2]->getName());
    }

    public function testGetUserClients_UserExistsWithoutClients_ReturnsEmptyCollection()
    {
        // Preparar datos de prueba
        $userId = 'some-uuid';
        $user = new User();
        $user->setUuid($userId);

        // El usuario no tiene clientes asociados

        // Configurar el mock del repositorio de usuarios
        $this->userRepositoryMock->method('findByUuid')
            ->with($userId)
            ->willReturn($user);

        // Ejecutar el caso de uso
        $result = $this->getUserClientsUseCase->getUserClients($userId);

        // Verificar el resultado
        $this->assertInstanceOf(ClientDTOCollection::class, $result);
        $this->assertCount(0, $result);
    }

    public function testGetUserClients_UserNotFound_ThrowsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('USER_NOT_FOUND');

        $userId = 'non-existent-uuid';

        // Configurar el mock del repositorio de usuarios para devolver null
        $this->userRepositoryMock->method('findByUuid')
            ->with($userId)
            ->willReturn(null);

        // Ejecutar el caso de uso
        $this->getUserClientsUseCase->getUserClients($userId);
    }
}