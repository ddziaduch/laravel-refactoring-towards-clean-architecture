<?php

namespace Conduit\Article\Adapter\Secondary\Eloquent;

use Conduit\Article\Application\Port\Secondary\ArticleRepository;
use Conduit\Article\Domain\Article;
use Conduit\Article\Domain\TagList;

final class EloquentArticleRepository implements ArticleRepository
{
    public function findBySlug(string $slug): ?Article
    {
        $model = ArticleModel::query()->with('tags')->where('slug', $slug)->first();

        if ($model === null) {
            return null;
        }

        return new Article(
            $model->getKey(),
            $model->user_id,
            $model->slug,
            $model->title,
            $model->description,
            $model->body,
            new TagList($model->tags->pluck('name')->all()),
        );
    }

    public function add(Article $article): int
    {
        $model = ArticleModel::query()->create(['user_id' => $article->authorId] + $this->attributes($article));
        $this->syncTags($model, $article->tags);

        return $model->getKey();
    }

    public function save(Article $article): void
    {
        $model = ArticleModel::query()->findOrFail($article->id);
        $model->update($this->attributes($article));
        $this->syncTags($model, $article->tags);
    }

    public function remove(Article $article): void
    {
        ArticleModel::query()->whereKey($article->id)->delete();
    }

    public function slugExists(string $slug, ?int $exceptArticleId = null): bool
    {
        return ArticleModel::query()
            ->where('slug', $slug)
            ->when($exceptArticleId !== null, fn ($query) => $query->whereKeyNot($exceptArticleId))
            ->exists();
    }

    private function attributes(Article $article): array
    {
        return [
            'slug' => $article->slug,
            'title' => $article->title,
            'description' => $article->description,
            'body' => $article->body,
        ];
    }

    private function syncTags(ArticleModel $model, TagList $tags): void
    {
        $model->tags()->sync(array_map(
            fn (string $name): int => TagModel::query()->firstOrCreate(['name' => $name])->getKey(),
            $tags->names,
        ));
    }
}
