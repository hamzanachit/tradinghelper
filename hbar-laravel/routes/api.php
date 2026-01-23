<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TradeController;
use App\Http\Controllers\Api\DrawingController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\WatchlistController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\CandleController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/candles', [CandleController::class, 'index']);
Route::get('/candles/latest', [CandleController::class, 'latest']);
Route::get('/24h', [CandleController::class, 'ticker24h']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/settings', [AuthController::class, 'updateSettings']);

    Route::apiResources([
        '/trades' => TradeController::class,
        '/drawings' => DrawingController::class,
        '/alerts' => AlertController::class,
        '/watchlist' => WatchlistController::class,
    ]);

    Route::get('/portfolio', [PortfolioController::class, 'index']);
});

Route::prefix('/admin')->group(function () {
    Route::post('/login', [AdminController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/stats', [AdminController::class, 'stats']);
        Route::get('/users', [AdminController::class, 'users']);
        Route::get('/payments', [AdminController::class, 'payments']);
        Route::get('/plans', [AdminController::class, 'plans']);
    });
});
