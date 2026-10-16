<?php

namespace Conduit\Article\Adapter\Primary\Http;

use Conduit\Article\Application\Port\Primary\FavoriteArticle;
use Conduit\Article\Application\Port\Primary\GetArticle;
use Conduit\Article\Application\Port\Primary\UnfavoriteArticle;
use Illuminate\Http\Request;

final readonly class FavoriteController
{
    use TranslatesArticleErrors;

    public function store(string $slug, Request $request, FavoriteArticle $favoriteArticle, GetArticle $getArticle): ArticleResource
    {
        return $this->translateErrors(function () use ($slug, $request, $favoriteArticle, $getArticle): ArticleResource {
            $favoriteArticle($slug, $this->viewerId($request));

            return new ArticleResource($getArticle($slug, $this->viewerId($request)));
        });
    }

    public function destroy(string $slug, Request $request, UnfavoriteArticle $unfavoriteArticle, GetArticle $getArticle): ArticleResource
    {
        return $this->translateErrors(function () use ($slug, $request, $unfavoriteArticle, $getArticle): ArticleResource {
            $unfavoriteArticle($slug, $this->viewerId($request));

            return new ArticleResource($getArticle($slug, $this->viewerId($request)));
        });
    }
}
