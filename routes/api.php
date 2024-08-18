<?php

use Illuminate\Support\Facades\Route;

Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/users', [\App\Http\Controllers\Auth\RegisterController::class, 'store']);
Route::put('/profile', [\App\Http\Controllers\Auth\ProfileController::class, 'update']);
Route::put('/password', [\App\Http\Controllers\Auth\UpdatePasswordController::class, 'update']);
Route::post('/forgot-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'send']);
Route::put('/reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'resetPassword']);

Route::get('menus/{menu:id}', [\App\Http\Controllers\Public\MenuController::class, 'show'])
     ->name('public.menu.show');

Route::middleware('auth:api')->group(function () {
    # restaurants
    Route::middleware('role:' . \App\Enums\Roles::ADMIN->value)
         ->apiResource('users', \App\Http\Controllers\UserController::class)->only('destroy');

    Route::apiResource('restaurants', \App\Http\Controllers\RestaurantController::class);

    Route::middleware('can:view,restaurant')
         ->as('restaurants.')
         ->prefix('restaurants/{restaurant:id}')
         ->scopeBindings()
         ->group(function () {
             # plates
             Route::apiResource('plates', \App\Http\Controllers\PlateController::class);

             # menus
             Route::apiResource('menus', \App\Http\Controllers\MenuController::class);
         });
});
