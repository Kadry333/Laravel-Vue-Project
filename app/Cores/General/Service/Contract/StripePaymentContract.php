<?php

namespace App\Cores\General\Service\Contract;

use Stripe\Checkout\Session;

interface StripePaymentContract
{

    public function createCheckoutSession(int $orderId, float $orderAmount): Session;
}
