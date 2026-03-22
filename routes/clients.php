<?php

use App\Http\Controllers\ClientDashboard\RoomController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('ClientDashboard/MakeReservation/Rooms');
});

Route::prefix('rooms')->name('rooms')->as('rooms.')->group(function () {

    Route::get('/', [RoomController::class, 'index'])->name('index');
});
