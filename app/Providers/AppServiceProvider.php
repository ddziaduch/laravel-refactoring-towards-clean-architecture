<?php

namespace App\Providers;

use Clean\Adapter\Out\Eloquent\EloquentArticleRepository;
use Clean\Application\Port\Out\ArticleRepository;
use Clean\Application\Port\In\DeleteArticleUseCasePort;
use Clean\Application\UseCase\DeleteArticleUseCase;
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
        $this->app->bind(DeleteArticleUseCasePort::class, DeleteArticleUseCase::class);
        $this->app->bind(ArticleRepository::class, EloquentArticleRepository::class);
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
