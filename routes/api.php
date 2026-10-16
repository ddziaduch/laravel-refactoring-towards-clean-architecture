<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('tags', [TagController::class, 'index']);
Route::get('articles/{article}/comments', [CommentController::class, 'index']);

Route::prefix('articles')->group(function () {
    Route::get('/', [ArticleController::class, 'index']);
    Route::get('feed', [ArticleController::class, 'feed'])->middleware('auth');
    Route::get('{article}', [ArticleController::class, 'show']);
});

Route::middleware('auth')->group(function () {
    Route::prefix('articles')->group(function () {
        Route::post('/', [ArticleController::class, 'store']);
        Route::put('{article}', [ArticleController::class, 'update']);
        Route::delete('{article}', [ArticleController::class, 'destroy']);
        Route::post('{article}/favorite', [ArticleController::class, 'favorite']);
        Route::delete('{article}/favorite', [ArticleController::class, 'unfavorite']);
    });

    Route::prefix('articles')->group(function () {
        Route::post('{article}/comments', [CommentController::class, 'store']);
        Route::delete('{article}/comments/{comment}', [CommentController::class, 'destroy'])
            ->scopeBindings();
    });
});
