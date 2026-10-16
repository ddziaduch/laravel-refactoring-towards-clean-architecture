<?php

use Conduit\Article\Adapter\Primary\Http\ArticleController;
use Conduit\Article\Adapter\Primary\Http\FavoriteController;
use Conduit\Article\Adapter\Primary\Http\ListTagsController;
use Illuminate\Support\Facades\Route;

Route::get('tags', ListTagsController::class);

// The feed must be registered before articles/{slug}.
Route::get('articles/feed', [ArticleController::class, 'feed'])->middleware('auth');
Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/{slug}', [ArticleController::class, 'show']);

Route::middleware('auth')->group(function () {
    Route::post('articles', [ArticleController::class, 'store']);
    Route::put('articles/{slug}', [ArticleController::class, 'update']);
    Route::delete('articles/{slug}', [ArticleController::class, 'destroy']);
    Route::post('articles/{slug}/favorite', [FavoriteController::class, 'store']);
    Route::delete('articles/{slug}/favorite', [FavoriteController::class, 'destroy']);
});
