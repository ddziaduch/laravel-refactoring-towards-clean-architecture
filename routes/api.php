<?php

use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::get('articles/{article}/comments', [CommentController::class, 'index']);

Route::middleware('auth')->group(function () {
    Route::prefix('articles')->group(function () {
        Route::post('{article}/comments', [CommentController::class, 'store']);
        Route::delete('{article}/comments/{comment}', [CommentController::class, 'destroy'])
            ->scopeBindings();
    });
});
