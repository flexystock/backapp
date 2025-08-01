<?php

namespace App\Stripe\Application\UseCases;

use App\Stripe\Application\DTO\PaymentMethodRequest;
use App\Stripe\Application\DTO\PaymentMethodResponse;
use App\Stripe\Application\InputPorts\PaymentMethodUseCaseInterface;
use App\Stripe\Application\OutputPorts\PaymentMethodUseRepositoryInterface;

class PaymentMethodUseCase implements PaymentMethodUseCaseInterface
{
    public function __construct(private PaymentMethodUseRepositoryInterface $repository)
    {
    }

    public function execute(PaymentMethodRequest $request): PaymentMethodResponse
    {
        $paymentMethodId = $this->repository->getDefaultPaymentMethod(
            $request->getUuidClient()
        );

        return new PaymentMethodResponse($paymentMethodId);
    }
}
