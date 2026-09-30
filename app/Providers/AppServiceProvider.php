<?php

namespace App\Providers;

use Clean\Adapter\Secondary\Eloquent\EloquentCommentRepository;
use Clean\Application\Port\Secondary\CommentRepository;
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
        $this->app->bind(CommentRepository::class, EloquentCommentRepository::class);

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
