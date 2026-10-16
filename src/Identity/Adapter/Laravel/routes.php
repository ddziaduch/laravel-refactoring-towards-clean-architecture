<?php

use Conduit\Identity\Adapter\Primary\Http\GetCurrentUserController;
use Conduit\Identity\Adapter\Primary\Http\LogInController;
use Conduit\Identity\Adapter\Primary\Http\RegisterUserController;
use Conduit\Identity\Adapter\Primary\Http\UpdateCurrentUserController;
use Illuminate\Support\Facades\Route;

Route::post('users', RegisterUserController::class);
Route::post('users/login', LogInController::class);

Route::middleware('auth')->group(function () {
    Route::get('user', GetCurrentUserController::class);
    Route::put('user', UpdateCurrentUserController::class);
});
