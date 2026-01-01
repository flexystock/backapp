<?php

declare(strict_types=1);

namespace App\ControlPanel\User\Application\UseCases;

use App\ControlPanel\User\Application\DTO\GetUserInfoRequest;
use App\ControlPanel\User\Application\DTO\GetUserInfoResponse;
use App\ControlPanel\User\Application\InputPorts\GetUserInfoUseCaseInterface;
use App\ControlPanel\User\Application\OutputPorts\UserRepositoryInterface;
use App\Entity\Main\User;
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
        $emailUser = $request->getEmailUser();

        if ($emailUser) {
            // Get specific user
            $this->logger->info("Executing ControlPanel GetUserInfoUseCase for user: {$emailUser}");
            $user = $this->userRepository->findOneByEmail($emailUser);

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

    private function mapUserToArray(User $user): array
    {
        $userCreatedBy = $user->getUuidUserCreation();
        $userCreatedBy = $this->userRepository->findOneByUuid($userCreatedBy);
        $userEmailCreatedBy = $userCreatedBy ? $userCreatedBy->getEmail() : null;
        $userUpdatedBy = $user->getUuidUserModification();
        $userUpdatedBy = $this->userRepository->findOneByUuid($userUpdatedBy);
        $userEmailUpdatedBy = $userUpdatedBy ? $userUpdatedBy->getEmail() : null;

        return [
            'uuid_user' => $user->getUuid(),
            'name' => $user->getName() ?? '',  // ← Añade ?? ''
            'surnames' => $user->getSurnames() ?? '',  // ← Añade ?? ''
            'phone' => $user->getPhone() ?? '',  // ← Añade ?? ''
            'email' => $user->getEmail(),
            'is_root' => $user->isRoot(),
            'active' => $user->isActive(),
            'document_type' => $user->getDocumentType() ?? '',  // ← Añade ?? ''
            'document_number' => $user->getDocumentNumber() ?? '',  // ← IMPORTANTE: Añade ?? ''
            'preferred_contact_method' => $user->getPreferredContactMethod() ?? '',  // ← Añade ?? ''
            'is_verified' => $user->isVerified(),
            'user_created_by' => $userEmailCreatedBy?? '',  // ← Añade ?? ''
            'user_updated_by' => $userEmailUpdatedBy?? '',  // ← Añade ?? ''
            'date_created' => $user->getDatehourCreation()?->format('Y-m-d H:i:s') ?? '',  // ← Añade ?? ''
            'date_updated' => $user->getDatehourModification()?->format('Y-m-d H:i:s') ?? '',  // ← Añade ?? ''
            'last_login' => $user->getLastAccess()?->format('Y-m-d H:i:s') ?? '',  // ← Añade ?? ''
        ];
    }
}
