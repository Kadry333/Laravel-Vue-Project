<?php

use App\Http\Controllers\AdminDashboard\RoomController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
// roles routes
Route::middleware(['auth', 'role:admin'])->get('/admin', function () {
    return Inertia::render('RolePage', ['role' => 'Admin']);
});


Route::middleware(['auth', 'role:manager'])->get('/manager', function () {
    return Inertia::render('RolePage', ['role' => 'Manager']);
});

Route::middleware(['auth', 'role:receptionist'])->get('/receptionist', function () {
    return Inertia::render('RolePage', ['role' => 'Receptionist']);
});

Route::middleware(['auth', 'role:client'])->get('/client', function () {
    return Inertia::render('RolePage', ['role' => 'Client']);
});

// ======================== Admin ====================
Route::prefix('admin')->as('admin.')->group(function () {


    Route::prefix('rooms')->as('rooms.')->group(function () {
        Route::get('/',         [RoomController::class, 'index'])->name('index');
        Route::post('/',        [RoomController::class, 'store'])->name('store');
        Route::put('/{room}',   [RoomController::class, 'update'])->name('update');
        Route::delete('/{room}', [RoomController::class, 'destroy'])->name('destroy');
    });
});
