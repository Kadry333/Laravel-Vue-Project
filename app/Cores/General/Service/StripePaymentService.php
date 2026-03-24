<?php

namespace App\Cores\General\Service;

use App\Cores\General\Service\Contract\StripePaymentContract;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripePaymentService implements StripePaymentContract
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckoutSession(int $orderId, float $orderAmount): Session
    {
        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Order #' . $orderId,
                    ],
                    'unit_amount' => $orderAmount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => config('services.stripe.success_url') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => config('services.stripe.cancel_url'),
            'metadata' => [
                'order_id' => $orderId,
            ],
        ]);
    }
}
