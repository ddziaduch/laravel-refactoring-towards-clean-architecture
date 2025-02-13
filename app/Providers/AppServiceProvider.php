<?php

namespace App\Providers;

use Clean\Adapter\Out\Eloquent\EloquentCommentRepository;
use Clean\Adapter\Out\Eloquent\EloquentGetCommentReadModel;
use Clean\Application\Port\Out\CommentRepository;
use Clean\Application\Port\Out\GetCommentReadModel;
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
        $this->app->bind(CommentRepository::class,EloquentCommentRepository::class);
        $this->app->bind(GetCommentReadModel::class,EloquentGetCommentReadModel::class);
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
