<?php

namespace App\Http\Controllers;

use App\Http\Requests\Article\DestroyRequest;
use App\Http\Requests\Article\FeedRequest;
use App\Http\Requests\Article\IndexRequest;
use App\Http\Requests\Article\StoreRequest;
use App\Http\Requests\Article\UpdateRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\User;
use App\Services\ArticleService;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    protected Article $article;

    protected ArticleService $articleService;

    protected User $user;

    public function __construct(Article $article, ArticleService $articleService, User $user)
    {
        $this->article = $article;
        $this->articleService = $articleService;
        $this->user = $user;
    }

    public function index(IndexRequest $request): ArticleCollection
    {
        $result = $this->article->getFiltered($request->validated());

        return new ArticleCollection($result['articles'], $result['count']);
    }

    public function feed(FeedRequest $request): ArticleCollection
    {
        $result = $this->article->getFiltered($request->validated(), auth()->user());

        return new ArticleCollection($result['articles'], $result['count']);
    }

    public function show(Article $article): ArticleResource
    {
        return $this->articleResponse($article);
    }

    public function store(StoreRequest $request): ArticleResource
    {
        $attributes = $request->validated()['article'];

        $article = auth()->user()->articles()->create($attributes);

        $this->syncTags($article, $attributes['tagList'] ?? []);

        return $this->articleResponse($article);
    }

    public function update(Article $article, UpdateRequest $request): ArticleResource
    {
        $attributes = $request->validated()['article'];

        $article->update($attributes);

        if (array_key_exists('tagList', $attributes)) {
            $this->syncTags($article, $attributes['tagList']);
        }

        return $this->articleResponse($article);
    }

    public function destroy(Article $article, DestroyRequest $request): Response
    {
        $article->delete();

        return response()->noContent();
    }

    public function favorite(Article $article): ArticleResource
    {
        $article->users()->syncWithoutDetaching(auth()->id());

        return $this->articleResponse($article);
    }

    public function unfavorite(Article $article): ArticleResource
    {
        $article->users()->detach(auth()->id());

        return $this->articleResponse($article);
    }

    protected function syncTags(Article $article, array $tags): void
    {
        $this->articleService->syncTags($article, $tags);
    }

    protected function articleResponse(Article $article): ArticleResource
    {
        return new ArticleResource(
            $article->load('user', 'users', 'tags', 'user.followers')->loadCount('users'),
        );
    }
}
