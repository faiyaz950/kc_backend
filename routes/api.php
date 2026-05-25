<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AnjumanController;
use App\Http\Controllers\Api\AnjumanTrackController;
use App\Http\Controllers\Api\ReciterController;
use App\Http\Controllers\Api\TrackController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/tracks', [TrackController::class, 'index']);
Route::get('/tracks/{id}', [TrackController::class, 'show']);
Route::get('/reciters', [ReciterController::class, 'index']);
Route::get('/reciters/{id}', [ReciterController::class, 'show']);
Route::get('/anjumans', [AnjumanController::class, 'index']);
Route::get('/anjumans/{id}', [AnjumanController::class, 'show']);
Route::get('/anjumans/{anjumanId}/tracks', [AnjumanTrackController::class, 'index']);

// Authenticated user routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'updateProfile']);

    Route::post('/favorites/{trackId}', [AuthController::class, 'addFavorite']);
    Route::delete('/favorites/{trackId}', [AuthController::class, 'removeFavorite']);
    Route::post('/recently-played/{trackId}', [AuthController::class, 'addRecentlyPlayed']);

    Route::post('/tracks/{id}/play', [TrackController::class, 'incrementPlayCount']);

    // Admin only routes
    Route::middleware('admin')->group(function () {
        Route::post('/tracks', [TrackController::class, 'store']);
        Route::post('/tracks/{id}', [TrackController::class, 'update']);
        Route::delete('/tracks/{id}', [TrackController::class, 'destroy']);

        Route::post('/reciters', [ReciterController::class, 'store']);
        Route::post('/reciters/{id}', [ReciterController::class, 'update']);
        Route::delete('/reciters/{id}', [ReciterController::class, 'destroy']);

        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        Route::post('/anjumans', [AnjumanController::class, 'store']);
        Route::post('/anjumans/{id}', [AnjumanController::class, 'update']);
        Route::delete('/anjumans/{id}', [AnjumanController::class, 'destroy']);
        Route::post('/anjumans/{anjumanId}/tracks', [AnjumanTrackController::class, 'store']);
        Route::delete('/anjuman-tracks/{id}', [AnjumanTrackController::class, 'destroy']);
    });
});
