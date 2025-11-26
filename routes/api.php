<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// User APIs
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');

// Public Event APIs
Route::get('events', [EventController::class, 'index']);
Route::get('events/{event}', [EventController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // Organizer only routes
    Route::middleware('role:organizer')->group(function () {
        Route::post('events', [EventController::class, 'store']);
        Route::put('events/{id}', [EventController::class, 'update']);
        Route::delete('events/{id}', [EventController::class, 'destroy']);

        // Ticket APIs (organizer only)
        Route::post('events/{event_id}/tickets', [TicketController::class, 'store']);
        Route::put('tickets/{id}', [TicketController::class, 'update']);
        Route::delete('tickets/{id}', [TicketController::class, 'destroy']);
    });

    // Customer routes
    Route::middleware('role:customer')->group(function () {
        // Booking APIs
        Route::post('tickets/{id}/bookings', [BookingController::class, 'store'])->middleware('prevent.double.booking');
        Route::get('bookings', [BookingController::class, 'index']);
        Route::put('bookings/{id}/cancel', [BookingController::class, 'cancel']);

        // Payment APIs
        Route::post('bookings/{id}/payment', [PaymentController::class, 'store']);
    });

    // Payment APIs (accessible to both roles)
    Route::get('payments/{payment}', [PaymentController::class, 'show']);
});
