<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;


// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail']);
    Route::post('/password/reset', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1');

    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{id}', [ArticleController::class, 'show']);
});


