<?php

use App\Http\Controllers\ClientDashboard\ReservationController;
use App\Http\Controllers\ClientDashboard\RoomController;
use App\Http\Controllers\ClientDashboard\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {

    return redirect()->route('dashboard');
})->name('home');

Route::prefix('rooms')->as('rooms.')->group(function () {

    Route::get('/', [RoomController::class , 'index'])->name('index');
});

Route::prefix('reservation')->as('reservation.')->group(function () {

    Route::get('/', [ReservationController::class , 'index'])->name('index');
    Route::post('/create', [ReservationController::class , 'create'])->name('create');
    Route::get('/success', [ReservationController::class , 'success'])->name('success');
    Route::get('/cancel', [ReservationController::class , 'cancel'])->name('cancel');


});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class , 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class , 'update'])->name('profile.update');
});
