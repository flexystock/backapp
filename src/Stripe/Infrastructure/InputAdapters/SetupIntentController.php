<?php

namespace App\Stripe\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use App\Stripe\Application\DTO\SetupIntentRequest;
use App\Stripe\Application\InputPorts\SetupIntentUseCaseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SetupIntentController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private SetupIntentUseCaseInterface $useCase,
        PermissionService $permissionService
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/payment_method/setup_intent', name: 'create_setup_intent', methods: ['POST'])]
    #[RequiresPermission('subscription.view')]
    public function __invoke(Request $request): JsonResponse
    {
        $permissionCheck = $this->checkPermissionJson('subscription.view');
        if ($permissionCheck) {
            return $permissionCheck;
        }

        $data = json_decode($request->getContent(), true);
        $uuidClient = $data['uuidClient'] ?? null;

        if (!$uuidClient) {
            return new JsonResponse(['message' => 'uuidClient requerido'], 400);
        }

        try {
            $response = $this->useCase->execute(new SetupIntentRequest($uuidClient));

            return new JsonResponse(['client_secret' => $response->clientSecret]);
        } catch (\Throwable $e) {
            return new JsonResponse(['message' => $e->getMessage()], 400);
        }
    }
}
