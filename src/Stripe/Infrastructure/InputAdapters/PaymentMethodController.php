<?php

namespace App\Stripe\Infrastructure\InputAdapters;

use App\Security\PermissionControllerTrait;
use App\Security\PermissionService;
use App\Security\RequiresPermission;
use App\Stripe\Application\DTO\PaymentMethodRequest;
use App\Stripe\Application\InputPorts\PaymentMethodUseCaseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PaymentMethodController extends AbstractController
{
    use PermissionControllerTrait;

    public function __construct(
        private PaymentMethodUseCaseInterface $useCase,
        PermissionService $permissionService
    ) {
        $this->permissionService = $permissionService;
    }

    #[Route('/api/payment_method/default', name: 'get_default_payment_method', methods: ['POST'])]
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
            return new JsonResponse(['message' => 'uuidClient required'], 400);
        }

        $response = $this->useCase->execute(new PaymentMethodRequest($uuidClient));

        if (!$response->paymentMethodId) {
            return new JsonResponse(['message' => 'No payment method found'], 404);
        }

        return new JsonResponse(['paymentMethodId' => $response->paymentMethodId]);
    }
}
