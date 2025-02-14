<?php

namespace App\Providers;

use Clean\Application\Port\In\CreateArticleUseCasePort;
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
