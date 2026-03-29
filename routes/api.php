<?php

use App\Http\Controllers\ClientDashboard\ReservationController;
use Illuminate\Support\Facades\Route;

Route::post('/stripe/webhook', [ReservationController::class, 'handle']);
