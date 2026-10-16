<?php

use Conduit\Engagement\Adapter\Primary\Http\CommentController;
use Illuminate\Support\Facades\Route;

Route::get('articles/{slug}/comments', [CommentController::class, 'index']);

Route::middleware('auth')->group(function () {
    Route::post('articles/{slug}/comments', [CommentController::class, 'store']);
    Route::delete('articles/{slug}/comments/{commentId}', [CommentController::class, 'destroy'])
        ->whereNumber('commentId');
});
