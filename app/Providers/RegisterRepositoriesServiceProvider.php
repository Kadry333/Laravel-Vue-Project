<?php

namespace App\Providers;

use App\Cores\General\Repository\FloorRepository;
use App\Cores\General\Repository\ReservationRepository;
use App\Cores\General\Repository\RoomRepository;
use App\Cores\General\RepositoryInterfaces\FloorRepositoryInterface;
use App\Cores\General\RepositoryInterfaces\ReservationRepositoryInterface;
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
        $this->app->bind(ReservationRepositoryInterface::class, ReservationRepository::class);
        $this->app->bind(FloorRepositoryInterface::class, FloorRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
