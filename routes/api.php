<?php

use App\Http\Controllers\AuctionController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('auction', [AuctionController::class, 'store']);
    Route::post('auction/{auction}/upload', [AuctionController::class, 'upload']);
    Route::get('auctions', [AuctionController::class, 'index']);
    Route::get('auction/{auction}', [AuctionController::class, 'show']);
    Route::put('auction/{auction}/bid', [AuctionController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'info']);
});
