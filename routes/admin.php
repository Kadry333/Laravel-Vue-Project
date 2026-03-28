<?php

use App\Http\Controllers\AdminDashboard\FloorController;
use App\Http\Controllers\AdminDashboard\ManagerController;
use App\Http\Controllers\AdminDashboard\ReceptionistController;
use App\Http\Controllers\AdminDashboard\RoomController;
use App\Http\Controllers\AdminDashboard\ReservationController;
use App\Http\Controllers\AdminDashboard\ClientController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth', 'logs-out-banned-user', 'role:admin|manager'])->group(function () {
    Route::get('/', fn() => Inertia::render('AdminDashboard/Admin'))->name('admin.index');


    Route::prefix('receptionists')->as('receptionists.')->group(function () {
            Route::get('/', [ReceptionistController::class , 'index'])->name('index');
            Route::get('/create', [ReceptionistController::class , 'create'])->name('create');
            Route::post('/', [ReceptionistController::class , 'store'])->name('store');
            Route::get('/{receptionist}/edit', [ReceptionistController::class , 'edit'])->name('edit');
            Route::patch('/{receptionist}', [ReceptionistController::class , 'update'])->name('update');
            Route::delete('/{receptionist}', [ReceptionistController::class , 'destroy'])->name('destroy');
            Route::patch('/{receptionist}/ban', [ReceptionistController::class , 'ban'])->name('ban');
            Route::patch('/{receptionist}/unban', [ReceptionistController::class , 'unban'])->name('unban');
        }
        );
    });

// ======================== Managers ====================
Route::prefix('managers')->as('managers.')->group(function () {
    Route::get('/', [ManagerController::class , 'index'])->name('index');
    Route::get('/create', [ManagerController::class , 'create'])->name('create');
    Route::post('/', [ManagerController::class , 'store'])->name('store');
    Route::get('/{manager}', [ManagerController::class , 'show'])->name('show');
    Route::get('/{manager}/edit', [ManagerController::class , 'edit'])->name('edit');
    Route::post('/{manager}', [ManagerController::class , 'update'])->name('update');
    Route::delete('/{manager}', [ManagerController::class , 'destroy'])->name('destroy');
});

Route::prefix('floors')->as('floors.')->group(function () {
    Route::get('/', [FloorController::class , 'index'])->name('index');
    Route::get('/create', [FloorController::class , 'create'])->name('create');
    Route::post('/', [FloorController::class , 'store'])->name('store');
    Route::get('/{floor}', [FloorController::class , 'show'])->name('show');
    Route::get('/{floor}/edit', [FloorController::class , 'edit'])->name('edit');
    Route::post('/{floor}', [FloorController::class , 'update'])->name('update');
    Route::delete('/{floor}', [FloorController::class , 'destroy'])->name('destroy');
});

Route::prefix('rooms')->as('rooms.')->group(function () {
    Route::get('/', [RoomController::class , 'index'])->name('index');
    Route::get('/create', [RoomController::class , 'create'])->name('create');
    Route::post('/', [RoomController::class , 'store'])->name('store');
    Route::get('/{room}/edit', [RoomController::class , 'edit'])->name('edit');
    Route::post('/{room}', [RoomController::class , 'update'])->name('update');
    Route::delete('/{room}', [RoomController::class , 'destroy'])->name('destroy');
});

Route::prefix('reservations')->as('reservations.')->group(function () {
    Route::get('/', [ReservationController::class , 'index'])->name('index');
    Route::patch('/{reservation}/status', [ReservationController::class , 'updateStatus'])->name('update-status');
});

Route::prefix('clients')->as('clients.')->group(function () {
    Route::get('/', [ClientController::class, 'index'])->name('index');
    Route::patch('/{client}/toggle-approval', [ClientController::class, 'toggleApproval'])->name('toggle-approval');
});
