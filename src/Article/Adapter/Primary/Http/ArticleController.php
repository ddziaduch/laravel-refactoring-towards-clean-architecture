<?php

namespace Conduit\Article\Adapter\Primary\Http;

use Conduit\Article\Application\Port\Primary\DeleteArticle;
use Conduit\Article\Application\Port\Primary\GetArticle;
use Conduit\Article\Application\Port\Primary\GetFeed;
use Conduit\Article\Application\Port\Primary\ListArticles;
use Conduit\Article\Application\Port\Primary\PublishArticle;
use Conduit\Article\Application\Port\Primary\UpdateArticle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as HttpStatus;

/**
 * Commands return a slug, then the response is read through GetArticle,
 * so writing and reading stay separate use cases.
 */
final readonly class ArticleController
{
    use TranslatesArticleErrors;

    public function index(ListArticlesRequest $request, ListArticles $listArticles): ArticleListResource
    {
        return new ArticleListResource($listArticles($request->filter(), $this->viewerId($request)));
    }

    public function feed(FeedRequest $request, GetFeed $getFeed): ArticleListResource
    {
        return new ArticleListResource($getFeed($this->viewerId($request), $request->limit(), $request->offset()));
    }

    public function show(string $slug, Request $request, GetArticle $getArticle): ArticleResource
    {
        return $this->translateErrors(
            fn (): ArticleResource => new ArticleResource($getArticle($slug, $this->viewerId($request))),
        );
    }

    public function store(PublishArticleRequest $request, PublishArticle $publishArticle, GetArticle $getArticle): JsonResponse
    {
        $slug = $publishArticle(
            $this->viewerId($request),
            $request->validated('article.title'),
            $request->validated('article.description'),
            $request->validated('article.body'),
            $request->validated('article.tagList', []),
        );

        return (new ArticleResource($getArticle($slug, $this->viewerId($request))))
            ->response()
            ->setStatusCode(HttpStatus::HTTP_CREATED);
    }

    public function update(
        string $slug,
        UpdateArticleRequest $request,
        UpdateArticle $updateArticle,
        GetArticle $getArticle,
    ): ArticleResource {
        return $this->translateErrors(function () use ($slug, $request, $updateArticle, $getArticle): ArticleResource {
            $newSlug = $updateArticle($slug, $this->viewerId($request), $request->changes());

            return new ArticleResource($getArticle($newSlug, $this->viewerId($request)));
        });
    }

    public function destroy(string $slug, Request $request, DeleteArticle $deleteArticle): Response
    {
        return $this->translateErrors(function () use ($slug, $request, $deleteArticle): Response {
            $deleteArticle($slug, $this->viewerId($request));

            return response()->noContent();
        });
    }
}
