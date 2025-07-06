<?php

namespace App\User\Application\UseCases\Profile;

use App\User\Application\DTO\Profile\GetUserInfoRequest;
use App\User\Application\DTO\Profile\GetUserInfoResponse;
use App\User\Application\InputPorts\Profile\GetUserInfoUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;

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
        $this->logger->info("Executing GetUserInfoUseCase for user: {$uuidUser}");
        $user = $this->userRepository->findOneByUuid($uuidUser);

        if (!$user) {
            return new GetUserInfoResponse(null, 'User not found', 404);
        }

        $userInfo = [
            'name'            => $user->getName(),
            'surnames'        => $user->getSurnames(),
            'phone'           => $user->getPhone(),
            'email'           => $user->getEmail(),
            'document_type'   => $user->getDocumentType(),
            'document_number' => $user->getDocumentNumber(),
        ];

        return new GetUserInfoResponse($userInfo, null, 200);
    }
}