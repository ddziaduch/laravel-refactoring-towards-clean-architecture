<?php

namespace App\Providers;

use Clean\Adapter\Out\EloquentArticleReadModelGetter;
use Clean\Adapter\Out\EloquentArticleRepository;
use Clean\Adapter\Out\StrSlugger;
use Clean\Application\Port\In\CreateArticleUseCasePort;
use Clean\Application\Port\Out\ArticleReadModelGetter;
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
        $this->app->bind(ArticleReadModelGetter::class, EloquentArticleReadModelGetter::class);
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
