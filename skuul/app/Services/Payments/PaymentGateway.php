<?php

namespace App\Services\Payments;

use App\Models\PaymentTransaction;
use App\Models\Subscription;

interface PaymentGateway
{
    public function initiateStkPush(Subscription $subscription, string $phone, float $amount): PaymentTransaction;
    public function handleCallback(array $payload): void;
}
