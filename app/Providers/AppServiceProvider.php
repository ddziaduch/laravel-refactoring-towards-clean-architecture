<?php

namespace App\Providers;

use Clean\Adapter\Out\CommentEloquentRepository;
use Clean\Adapter\Out\CommentReadModelEloquentFinder;
use Clean\Application\Port\Out\CommentReadModelFinder;
use Clean\Domain\Port\Out\CommentRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            CommentRepository::class,
            CommentEloquentRepository::class,
        );

        $this->app->bind(
            CommentReadModelFinder::class,
            CommentReadModelEloquentFinder::class,
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
