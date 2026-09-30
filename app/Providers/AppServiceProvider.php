<?php

namespace App\Providers;

use Clean\Adapter\Secondary\Eloquent\EloquentArticleRepository;
use Clean\Adapter\Secondary\Eloquent\EloquentCommentRepository;
use Clean\Adapter\Secondary\Eloquent\EloquentGetCommentReadModel;
use Clean\Application\Port\Primary\CreateComment;
use Clean\Application\Port\Secondary\ArticleRepository;
use Clean\Application\Port\Secondary\CommentRepository;
use Clean\Application\Port\Secondary\GetCommentReadModel;
use Clean\Application\UseCase\CreateCommentUseCase;
use Illuminate\Support\ServiceProvider;
use Tymon\JWTAuth\Http\Parser\AuthHeaders;
use Tymon\JWTAuth\Http\Parser\Parser;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CreateComment::class, CreateCommentUseCase::class);
        $this->app->bind(ArticleRepository::class, EloquentArticleRepository::class);
        $this->app->bind(CommentRepository::class, EloquentCommentRepository::class);
        $this->app->bind(GetCommentReadModel::class, EloquentGetCommentReadModel::class);

        $this->app->extend('tymon.jwt.parser', function (Parser $parser): Parser {
            return $parser->addParser(
                (new AuthHeaders)->setHeaderPrefix('token'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
