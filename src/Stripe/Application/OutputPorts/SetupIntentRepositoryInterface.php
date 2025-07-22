<?php

namespace App\Stripe\Application\OutputPorts;

interface SetupIntentRepositoryInterface
{
    public function createSetupIntent(string $uuidClient): string;
}
