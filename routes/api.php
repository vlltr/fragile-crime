<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('auth/register', App\Http\Controllers\Auth\RegisterController::class);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('owner/properties', [\App\Http\Controllers\Owner\PropertyController::class, 'index']);
    Route::post('owner/properties', [\App\Http\Controllers\Owner\PropertyController::class, 'store']);

    Route::get('user/bookings', [\App\Http\Controllers\User\BookingController::class, 'index']);
});

Route::get('search', \App\Http\Controllers\Public\PropertySearchController::class);
Route::get(
    'properties/{property}',
    \App\Http\Controllers\Public\PropertyController::class
);
