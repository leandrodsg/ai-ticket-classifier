<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard route (read operations - higher limit)
Route::middleware(['throttle:30,1'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/api/dashboard/stats', [DashboardController::class, 'index'])
        ->name('api.dashboard.stats');
});

// Tickets CRUD routes (write operations - lower limit)
Route::middleware(['throttle:10,1'])->group(function () {
    Route::resource('tickets', TicketController::class);
});
