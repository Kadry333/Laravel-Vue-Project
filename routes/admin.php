<?php

use App\Http\Controllers\AdminDashboard\ManagerController;
use App\Http\Controllers\AdminDashboard\RoomController;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


// ======================== Rooms ====================
Route::prefix('rooms')->as('rooms.')->group(function () {
    Route::get('/',              [RoomController::class, 'index'])->name('index');
    Route::get('/create',        [RoomController::class, 'create'])->name('create');
    Route::post('/',             [RoomController::class, 'store'])->name('store');
    Route::get('/{room}/edit',   [RoomController::class, 'edit'])->name('edit');
    Route::post('/{room}',       [RoomController::class, 'update'])->name('update');
    Route::delete('/{room}',     [RoomController::class, 'destroy'])->name('destroy');
});

// ======================== Managers ====================
Route::prefix('managers')->as('managers.')->group(function () {
    Route::get('/',                 [ManagerController::class, 'index'])->name('index');
    Route::get('/create',           [ManagerController::class, 'create'])->name('create');
    Route::post('/',                [ManagerController::class, 'store'])->name('store');
    Route::get('/{manager}',        [ManagerController::class, 'show'])->name('show');
    Route::get('/{manager}/edit',   [ManagerController::class, 'edit'])->name('edit');
    Route::post('/{manager}',       [ManagerController::class, 'update'])->name('update');
    Route::delete('/{manager}',     [ManagerController::class, 'destroy'])->name('destroy');
});

