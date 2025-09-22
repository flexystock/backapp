<?php

namespace App\Subscription\Application\DTO;

class CreateStripeCheckoutSessionRequest
{
    private string $priceId;
    private string $planId;
    private string $successUrl;
    private string $cancelUrl;
    private ?string $userUuid = null;

    public function getPriceId(): string
    {
        return $this->priceId;
    }

    public function setPriceId(string $priceId): void
    {
        $this->priceId = $priceId;
    }

    public function getPlanId(): string
    {
        return $this->planId;
    }

    public function setPlanId(string $planId): void
    {
        $this->planId = $planId;
    }

    public function getSuccessUrl(): string
    {
        return $this->successUrl;
    }

    public function setSuccessUrl(string $successUrl): void
    {
        $this->successUrl = $successUrl;
    }

    public function getCancelUrl(): string
    {
        return $this->cancelUrl;
    }

    public function setCancelUrl(string $cancelUrl): void
    {
        $this->cancelUrl = $cancelUrl;
    }

    public function getUserUuid(): ?string
    {
        return $this->userUuid;
    }

    public function setUserUuid(?string $userUuid): void
    {
        $this->userUuid = $userUuid;
    }
}
