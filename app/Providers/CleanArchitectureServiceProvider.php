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

/**
 * Wires the application ports to their adapters.
 */
class CleanArchitectureServiceProvider extends ServiceProvider
{
    public array $bindings = [
        CreateComment::class => CreateCommentUseCase::class,
        ArticleRepository::class => EloquentArticleRepository::class,
        CommentRepository::class => EloquentCommentRepository::class,
        GetCommentReadModel::class => EloquentGetCommentReadModel::class,
    ];
}
