<?php

namespace App\Providers;

use Clean\Adapter\Out\EloquentArticleReadModelFinder;
use Clean\Adapter\Out\EloquentArticleRepository;
use Clean\Adapter\Out\StrSlugger;
use Clean\Application\Port\In\CreateArticleUseCasePort;
use Clean\Application\Port\Out\ArticleReadModelFinder;
use Clean\Application\Port\Out\ArticleRepository;
use Clean\Application\Port\Out\Slugger;
use Clean\Application\UseCase\CreateArticleUseCase;
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
        $this->app->bind(CreateArticleUseCasePort::class, CreateArticleUseCase::class);
        $this->app->bind(Slugger::class, StrSlugger::class);
        $this->app->bind(ArticleRepository::class, EloquentArticleRepository::class);
        $this->app->bind(ArticleReadModelFinder::class, EloquentArticleReadModelFinder::class);
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
