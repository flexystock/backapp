<?php

namespace App\Subscription\Application\UseCases;

use App\Entity\Main\User;
use App\Subscription\Application\DTO\CreateStripeCheckoutSessionRequest;
use App\Subscription\Application\DTO\CreateStripeCheckoutSessionResponse;
use App\Subscription\Application\InputPorts\CreateStripeCheckoutSessionUseCaseInterface;
use App\Subscription\Application\OutputPorts\StripeServiceInterface;
use App\User\Application\OutputPorts\Repositories\UserRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateStripeCheckoutSessionUseCase implements CreateStripeCheckoutSessionUseCaseInterface
{
    public function __construct(
        private StripeServiceInterface $stripeService,
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $logger
    ) {
    }

    public function execute(CreateStripeCheckoutSessionRequest $request): CreateStripeCheckoutSessionResponse
    {
        $response = new CreateStripeCheckoutSessionResponse();

        try {
            if (!$request->getUserUuid()) {
                $response->setError('User UUID is required');
                $response->setStatusCode(400);

                return $response;
            }

            $user = $this->userRepository->findOneBy(['uuid_user' => $request->getUserUuid()]);
            if (!$user instanceof User) {
                $response->setError('User not found');
                $response->setStatusCode(404);

                return $response;
            }

            $clientUuid = $user->getSelectedClientUuid();
            if (!$clientUuid) {
                $response->setError('User has no selected client');
                $response->setStatusCode(400);

                return $response;
            }

            $sessionData = $this->stripeService->createCheckoutSession(
                $request->getPriceId(),
                $user->getEmail(),
                $clientUuid,
                $request->getPlanId(),
                $request->getSuccessUrl(),
                $request->getCancelUrl()
            );

            if (isset($sessionData['url'])) {
                $response->setUrl($sessionData['url']);
            } else {
                $response->setError('Failed to create checkout session');
                $response->setStatusCode(500);
            }
        } catch (\Throwable $e) {
            $this->logger->error('Error creating Stripe checkout session', [
                'exception' => $e->getMessage(),
                'user_uuid' => $request->getUserUuid(),
            ]);

            $response->setError('Internal server error');
            $response->setStatusCode(500);
        }

        return $response;
    }
}
