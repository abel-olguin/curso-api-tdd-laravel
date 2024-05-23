<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [\App\Http\Controllers\LoginController::class, 'login']);
Route::post('/users', [\App\Http\Controllers\RegisterController::class, 'store']);
Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update']);
