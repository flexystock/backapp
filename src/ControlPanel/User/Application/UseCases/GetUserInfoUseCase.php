<?php

namespace App\ControlPanel\User\Application\UseCases;

use App\ControlPanel\User\Application\DTO\GetUserInfoRequest;
use App\ControlPanel\User\Application\DTO\GetUserInfoResponse;
use App\ControlPanel\User\Application\InputPorts\GetUserInfoUseCaseInterface;
use App\ControlPanel\User\Application\OutputPorts\UserRepositoryInterface;
use Psr\Log\LoggerInterface;

class GetUserInfoUseCase implements GetUserInfoUseCaseInterface
{
    private LoggerInterface $logger;
    private UserRepositoryInterface $userRepository;

    public function __construct(LoggerInterface $logger, UserRepositoryInterface $userRepository)
    {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
    }

    public function execute(GetUserInfoRequest $request): GetUserInfoResponse
    {
        $uuidUser = $request->getUuidUser();

        if ($uuidUser) {
            // Get specific user
            $this->logger->info("Executing ControlPanel GetUserInfoUseCase for user: {$uuidUser}");
            $user = $this->userRepository->findOneByUuid($uuidUser);

            if (!$user) {
                return new GetUserInfoResponse(null, 'User not found', 404);
            }

            $userInfo = $this->mapUserToArray($user);

            return new GetUserInfoResponse([$userInfo], null, 200);
        } else {
            // Get all users
            $this->logger->info('Executing ControlPanel GetUserInfoUseCase for all users');
            $users = $this->userRepository->findAll();

            $usersInfo = array_map(function ($user) {
                return $this->mapUserToArray($user);
            }, $users);

            return new GetUserInfoResponse($usersInfo, null, 200);
        }
    }

    private function mapUserToArray($user): array
    {
        return [
            'uuid_user' => $user->getUuid(),
            'name' => $user->getName(),
            'surnames' => $user->getSurnames(),
            'phone' => $user->getPhone(),
            'email' => $user->getEmail(),
            'is_root' => $user->isRoot(),
            'active' => $user->isActive(),
            'document_type' => $user->getDocumentType(),
            'document_number' => $user->getDocumentNumber(),
            'preferred_contact_method' => $user->getPreferredContactMethod(),
            'is_verified' => $user->isVerified(),
        ];
    }
}
