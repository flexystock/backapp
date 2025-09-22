<?php

namespace App\Subscription\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use App\Subscription\Application\DTO\CreateStripeCheckoutSessionRequest;
use App\Subscription\Application\InputPorts\CreateStripeCheckoutSessionUseCaseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[RequiresPermission('subscription.create')]
class CreateStripeCheckoutSessionController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private CreateStripeCheckoutSessionUseCaseInterface $createStripeCheckoutSessionUseCase,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private LoggerInterface $logger,
        private PermissionService $permissionService
    ) {
    }

    #[Route('/api/stripe/create-checkout-session', name: 'api_stripe_create_checkout_session', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $permissionCheck = $this->checkPermissionJson('subscription.create');
            if ($permissionCheck) {
                return $permissionCheck;
            }

            $dto = $this->serializer->deserialize($request->getContent(), CreateStripeCheckoutSessionRequest::class, 'json');
            $errors = $this->validator->validate($dto);
            if ($errors->count() > 0) {
                return new JsonResponse([
                    'status' => 'error',
                    'errors' => json_decode($this->serializer->serialize($errors, 'json'), true),
                ], Response::HTTP_BAD_REQUEST);
            }

            $user = $this->getUser();
            $dto->setUserUuid($user->getUuid());

            $responseDto = $this->createStripeCheckoutSessionUseCase->execute($dto);

            if ($responseDto->getError()) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => $responseDto->getError(),
                ], $responseDto->getStatusCode());
            }

            return new JsonResponse([
                'status' => 'success',
                'url' => $responseDto->getUrl(),
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            $this->logger->error('Error creating Stripe checkout session', ['exception' => $e->getMessage()]);

            return new JsonResponse([
                'status' => 'error',
                'message' => 'Internal Server Error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
