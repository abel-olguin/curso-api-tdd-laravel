<?php

use Illuminate\Support\Facades\Route;

Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/users', [\App\Http\Controllers\Auth\RegisterController::class, 'store']);
Route::put('/profile', [\App\Http\Controllers\Auth\ProfileController::class, 'update']);
Route::put('/password', [\App\Http\Controllers\Auth\UpdatePasswordController::class, 'update']);
Route::post('/reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'send']);
Route::put('/reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'resetPassword']);

# restaurants
Route::apiResource('restaurants', \App\Http\Controllers\RestaurantController::class);
