<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GiftController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'authenticateLogin']);
    Route::post('/logout', [AuthController::class, 'logoutUser']);
});


Route::group(['prefix' => 'gifts'], function () {
    Route::get('/', [GiftController::class, 'fetchGifts']);
    Route::post('/', [GiftController::class, 'createGift']);
    Route::get('/{giftId}', [GiftController::class, 'viewGift']);
})->middleware('auth:sanctum');
