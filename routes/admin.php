<?php

use App\Http\Controllers\AdminDashboard\ManagerController;
use App\Http\Controllers\AdminDashboard\ReceptionistController;
use App\Http\Controllers\AdminDashboard\RoomController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'logs-out-banned-user', 'role:admin|manager'])->group(function () {
    Route::get('/', fn () => Inertia::render('AdminDashboard/Admin'))->name('dashboard');

    Route::prefix('rooms')->as('rooms.')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('index');
        Route::get('/create', [RoomController::class, 'create'])->name('create');
        Route::post('/', [RoomController::class, 'store'])->name('store');
        Route::get('/{room}/edit', [RoomController::class, 'edit'])->name('edit');
        Route::post('/{room}', [RoomController::class, 'update'])->name('update');
        Route::delete('/{room}', [RoomController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('receptionists')->as('receptionists.')->group(function () {
        Route::get('/', [ReceptionistController::class, 'index'])->name('index');
        Route::get('/create', [ReceptionistController::class, 'create'])->name('create');
        Route::post('/', [ReceptionistController::class, 'store'])->name('store');
        Route::get('/{receptionist}/edit', [ReceptionistController::class, 'edit'])->name('edit');
        Route::patch('/{receptionist}', [ReceptionistController::class, 'update'])->name('update');
        Route::delete('/{receptionist}', [ReceptionistController::class, 'destroy'])->name('destroy');
        Route::patch('/{receptionist}/ban', [ReceptionistController::class, 'ban'])->name('ban');
        Route::patch('/{receptionist}/unban', [ReceptionistController::class, 'unban'])->name('unban');
    });
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

