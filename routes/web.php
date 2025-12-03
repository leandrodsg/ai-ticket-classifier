<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

// Dashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

// API endpoint for dashboard data with rate limiting
Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/api/dashboard/stats', [DashboardController::class, 'index'])
        ->name('api.dashboard.stats');
});

// Tickets CRUD routes with rate limiting
Route::middleware(['throttle:60,1'])->group(function () {
    Route::resource('tickets', TicketController::class);
});
