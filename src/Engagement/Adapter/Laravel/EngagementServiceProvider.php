<?php

namespace Conduit\Engagement\Adapter\Laravel;

use Conduit\Engagement\Adapter\Primary\Cli\AddCommentCommand;
use Conduit\Engagement\Adapter\Secondary\Article\ArticleApiLookup;
use Conduit\Engagement\Adapter\Secondary\Eloquent\EloquentCommentQueries;
use Conduit\Engagement\Adapter\Secondary\Eloquent\EloquentCommentRepository;
use Conduit\Engagement\Adapter\Secondary\Profile\ProfileAuthors;
use Conduit\Engagement\Application\Port\Primary\AddComment;
use Conduit\Engagement\Application\Port\Primary\DeleteComment;
use Conduit\Engagement\Application\Port\Primary\GetComment;
use Conduit\Engagement\Application\Port\Primary\ListComments;
use Conduit\Engagement\Application\Port\Secondary\ArticleLookup;
use Conduit\Engagement\Application\Port\Secondary\AuthorProfiles;
use Conduit\Engagement\Application\Port\Secondary\CommentQueries;
use Conduit\Engagement\Application\Port\Secondary\CommentRepository;
use Conduit\Engagement\Application\UseCase\AddCommentUseCase;
use Conduit\Engagement\Application\UseCase\DeleteCommentUseCase;
use Conduit\Engagement\Application\UseCase\GetCommentUseCase;
use Conduit\Engagement\Application\UseCase\ListCommentsUseCase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Engagement publishes no API: no other module depends on it.
 */
final class EngagementServiceProvider extends ServiceProvider
{
    public array $bindings = [
        // Primary ports
        AddComment::class => AddCommentUseCase::class,
        GetComment::class => GetCommentUseCase::class,
        ListComments::class => ListCommentsUseCase::class,
        DeleteComment::class => DeleteCommentUseCase::class,

        // Secondary ports
        ArticleLookup::class => ArticleApiLookup::class,
        AuthorProfiles::class => ProfileAuthors::class,
        CommentRepository::class => EloquentCommentRepository::class,
        CommentQueries::class => EloquentCommentQueries::class,
    ];

    public function boot(): void
    {
        Route::middleware('api')->prefix('api')->group(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->commands([AddCommentCommand::class]);
        }
    }
}
