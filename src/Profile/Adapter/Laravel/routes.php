<?php

use Conduit\Profile\Adapter\Primary\Http\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('profiles/{username}', [ProfileController::class, 'show']);

Route::middleware('auth')->group(function () {
    Route::post('profiles/{username}/follow', [ProfileController::class, 'follow']);
    Route::delete('profiles/{username}/follow', [ProfileController::class, 'unfollow']);
});
