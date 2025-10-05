<?php

declare(strict_types=1);

namespace App\User\Application\UseCases\Management;

use App\Entity\Main\User;
use App\Entity\Main\UserHistory;
use App\User\Application\DTO\Management\DeleteUserRequest;
use App\User\Application\InputPorts\DeleteUserInputPort;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DeleteUserUseCase implements DeleteUserInputPort
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly EntityManagerInterface $entityManager,
    )
    {
    }

    public function delete(DeleteUserRequest $request): void
    {
        $user = $this->userRepository->findByEmail($request->getUserEmail());
        if (null === $user) {
            throw new \RuntimeException('USER_NOT_FOUND');
        }

        $isAssociatedWithClient = false;
        foreach ($user->getClients() as $client) {
            if ($client->getUuidClient() === $request->getUuidClient()) {
                $isAssociatedWithClient = true;
                break;
            }
        }

        if (false === $isAssociatedWithClient) {
            throw new \RuntimeException('USER_NOT_ASSOCIATED_WITH_CLIENT');
        }

        foreach ($user->getRoleEntities()->toArray() as $role) {
            $user->removeRole($role);
        }

        foreach ($user->getClients()->toArray() as $client) {
            $user->removeClient($client);
        }

        $history = new UserHistory();
        $history->setUuidUser((string) $user->getUuid());
        $history->setUuidUserModification($request->getUuidUserModification());
        $history->setDataUserBeforeModification($this->encodeHistoryPayload($this->buildUserSnapshot($user)));
        $history->setDataUserAfterModification($this->encodeHistoryPayload(['deleted' => true]));
        $history->setDateModification(new \DateTimeImmutable());

        $this->entityManager->persist($history);

        $this->userRepository->delete($user);
    }

    private function buildUserSnapshot(User $user): array
    {
        return [
            'uuid' => $user->getUuid(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'surnames' => $user->getSurnames(),
            'active' => $user->isActive(),
            'roles' => array_map(static fn($role) => $role->getName(), $user->getRoleEntities()->toArray()),
            'clients' => array_map(static fn($client) => $client->getUuidClient(), $user->getClients()->toArray()),
        ];
    }

    private function encodeHistoryPayload(array $data): string
    {
        $encoded = json_encode($data);
        if (false === $encoded) {
            throw new \RuntimeException('USER_HISTORY_SERIALIZATION_FAILED');
        }

        return $encoded;
    }
}
