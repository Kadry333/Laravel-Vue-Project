<?php

namespace App\Providers;

use App\Cores\General\Service\Contract\StripePaymentContract;
use App\Cores\General\Service\StripePaymentService;
use Illuminate\Support\ServiceProvider;

class RegisterServicesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(StripePaymentContract::class, StripePaymentService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
