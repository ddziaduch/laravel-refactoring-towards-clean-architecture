<?php

namespace App\Providers;

use Clean\Adapter\Outgoing\LaravelArticleRepository;
use Clean\Adapter\Outgoing\LaravelCommentRepository;
use Clean\Application\Port\Ingoing\AddComment;
use Clean\Application\Port\Outgoing\ArticleRepository;
use Clean\Application\Port\Outgoing\CommentRepository;
use Clean\Application\UseCase\AddCommentUseCase;
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
        $this->app->bind(
            CommentRepository::class,
            LaravelCommentRepository::class
        );

        $this->app->bind(
            ArticleRepository::class,
            LaravelArticleRepository::class
        );

        $this->app->bind(
            AddComment::class,
            AddCommentUseCase::class
        );

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
