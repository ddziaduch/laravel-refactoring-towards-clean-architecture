<?php

namespace App\Providers;

use Clean\Application\Port\UseCase\CreateCommentUseCasePort;
use Clean\Application\UseCase\CreateCommentUseCase;
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
        $this->app->bind(CreateCommentUseCasePort::class,CreateCommentUseCase::class);
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
