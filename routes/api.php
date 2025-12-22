<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Contact form
Route::post('/contact', [ContactController::class, 'store']);

// Booking form ✅ (THIS WAS MISSING)
Route::post('/bookings', [BookingController::class, 'store']);

// Admin login
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| PROTECTED ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});

    // Contacts (admin)
    Route::get('/contacts', [ContactController::class, 'index']);

    // Bookings (admin)
    Route::get('/bookings', [BookingController::class, 'index']);
