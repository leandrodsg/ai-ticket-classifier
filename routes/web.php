<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;

Route::get('/', function () {
    return view('welcome');
});

// Tickets CRUD routes with rate limiting
Route::middleware(['throttle:60,1'])->group(function () {
    Route::resource('tickets', TicketController::class);
});
