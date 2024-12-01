<?php

namespace App\Tests\User\Application\UseCases;

use App\Client\Application\OutputPorts\Repositories\ClientRepositoryInterface;
use PHPUnit\Framework\TestCase;
use App\User\Application\UseCases\GetUserClientsUseCase;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use App\Client\Application\DTO\ClientDTOCollection;
use App\Entity\Main\User;
use App\Entity\Main\Client;
use Doctrine\Common\Collections\ArrayCollection;

class GetUserClientsUseCaseTest extends TestCase
{
    private UserRepositoryInterface $userRepositoryMock;
    private GetUserClientsUseCase $getUserClientsUseCase;
    private ClientRepositoryInterface $clientRepositoryMock;

    protected function setUp(): void
    {
        // Crear mock del repositorio de usuarios
        $this->userRepositoryMock = $this->createMock(UserRepositoryInterface::class);
        $this->clientRepositoryMock = $this->createMock(ClientRepositoryInterface::class);

        // Crear la instancia del caso de uso con el repositorio mockeado
        $this->getUserClientsUseCase = new GetUserClientsUseCase(
            $this->userRepositoryMock,
            $this->clientRepositoryMock
        );
    }


    public function testGetUserClients_UserExistsWithClients_ReturnsClients()
    {
        // Preparar datos de prueba
        $userId = 'some-uuid';
        $user = new User();
        $user->setUuid($userId);

        $client1 = new Client();
        $client1->setUuidClient('client-uuid-1');
        $client1->setName('Client 1');

        $client2 = new Client();
        $client2->setUuidClient('client-uuid-2');
        $client2->setName('Client 2');

        // Simular la relación entre usuario y clientes
        $user->addClient($client1);
        $user->addClient($client2);

        // Configurar el mock del repositorio de usuarios
        $this->userRepositoryMock->method('findByUuid')
            ->with($userId)
            ->willReturn($user);

        // Ejecutar el caso de uso
        $result = $this->getUserClientsUseCase->getUserClients($userId);

        // Verificar el resultado
        $this->assertInstanceOf(ClientDTOCollection::class, $result);
        $this->assertCount(2, $result);

        $clientDTOs = $result->toArray();
        $this->assertEquals('client-uuid-1', $clientDTOs[0]->uuid);
        $this->assertEquals('Client 1', $clientDTOs[0]->name);
        $this->assertEquals('client-uuid-2', $clientDTOs[1]->uuid);
        $this->assertEquals('Client 2', $clientDTOs[1]->name);
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
        $this->expectExceptionMessage('Usuario no encontrado');

        $userId = 'non-existent-uuid';

        // Configurar el mock del repositorio de usuarios para devolver null
        $this->userRepositoryMock->method('findByUuid')
            ->with($userId)
            ->willReturn(null);

        // Ejecutar el caso de uso
        $this->getUserClientsUseCase->getUserClients($userId);
    }

}