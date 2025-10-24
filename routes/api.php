<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GiftController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'authenticateLogin']);

Route::group(['prefix' => 'gifts'], function () {
    Route::get('/', [GiftController::class, 'fetchGifts']);
    Route::post('/create', [GiftController::class, 'createGift']);
})->middleware('auth:sanctum');
