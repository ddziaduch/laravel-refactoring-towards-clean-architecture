<?php

namespace Conduit\Article\Adapter\Laravel;

use Conduit\Article\Adapter\Secondary\Database\DatabaseArticleQueries;
use Conduit\Article\Adapter\Secondary\Database\DatabaseFavoriteRepository;
use Conduit\Article\Adapter\Secondary\Eloquent\EloquentArticleRepository;
use Conduit\Article\Adapter\Secondary\Laravel\StrSlugger;
use Conduit\Article\Adapter\Secondary\Profile\ProfileAuthors;
use Conduit\Article\Application\Port\Primary\DeleteArticle;
use Conduit\Article\Application\Port\Primary\FavoriteArticle;
use Conduit\Article\Application\Port\Primary\GetArticle;
use Conduit\Article\Application\Port\Primary\GetFeed;
use Conduit\Article\Application\Port\Primary\ListArticles;
use Conduit\Article\Application\Port\Primary\ListTags;
use Conduit\Article\Application\Port\Primary\PublishArticle;
use Conduit\Article\Application\Port\Primary\UnfavoriteArticle;
use Conduit\Article\Application\Port\Primary\UpdateArticle;
use Conduit\Article\Application\Port\Secondary\ArticleQueries;
use Conduit\Article\Application\Port\Secondary\ArticleRepository;
use Conduit\Article\Application\Port\Secondary\AuthorProfiles;
use Conduit\Article\Application\Port\Secondary\FavoriteRepository;
use Conduit\Article\Application\Port\Secondary\Followings;
use Conduit\Article\Application\Port\Secondary\Slugger;
use Conduit\Article\Application\PublicApi\ArticlesService;
use Conduit\Article\Application\UseCase\DeleteArticleUseCase;
use Conduit\Article\Application\UseCase\FavoriteArticleUseCase;
use Conduit\Article\Application\UseCase\GetArticleUseCase;
use Conduit\Article\Application\UseCase\GetFeedUseCase;
use Conduit\Article\Application\UseCase\ListArticlesUseCase;
use Conduit\Article\Application\UseCase\ListTagsUseCase;
use Conduit\Article\Application\UseCase\PublishArticleUseCase;
use Conduit\Article\Application\UseCase\UnfavoriteArticleUseCase;
use Conduit\Article\Application\UseCase\UpdateArticleUseCase;
use Conduit\Article\PublicApi\Articles;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class ArticleServiceProvider extends ServiceProvider
{
    public array $bindings = [
        // Public API
        Articles::class => ArticlesService::class,

        // Primary ports
        ListArticles::class => ListArticlesUseCase::class,
        GetFeed::class => GetFeedUseCase::class,
        GetArticle::class => GetArticleUseCase::class,
        PublishArticle::class => PublishArticleUseCase::class,
        UpdateArticle::class => UpdateArticleUseCase::class,
        DeleteArticle::class => DeleteArticleUseCase::class,
        FavoriteArticle::class => FavoriteArticleUseCase::class,
        UnfavoriteArticle::class => UnfavoriteArticleUseCase::class,
        ListTags::class => ListTagsUseCase::class,

        // Secondary ports
        ArticleRepository::class => EloquentArticleRepository::class,
        FavoriteRepository::class => DatabaseFavoriteRepository::class,
        ArticleQueries::class => DatabaseArticleQueries::class,
        AuthorProfiles::class => ProfileAuthors::class,
        Followings::class => ProfileAuthors::class,
        Slugger::class => StrSlugger::class,
    ];

    public function boot(): void
    {
        Route::middleware('api')->prefix('api')->group(__DIR__.'/routes.php');
    }
}
