<?php

namespace App\Providers;

use App\Cores\General\Repository\RoomRepository;
use App\Cores\General\RepositoryInterfaces\RoomRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RegisterRepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

        $this->app->bind(RoomRepositoryInterface::class, RoomRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
