<?php

declare(strict_types=1);

namespace Clean\Adapter\Out;

use App\Models\Article as EloquentArticle;
use App\Models\Tag;
use App\Models\User;
use Clean\Application\Port\Out\ArticleRepository;
use Clean\Domain\Entity\Article;

final class EloquentArticleRepository implements ArticleRepository
{
    public function save(Article $article): EloquentArticle
    {
        $user = User::where('id', $article->authorId)->firstOrFail();
        assert($user instanceof User);

        $eloquentArticle = $user->articles()->create([
            'title' => $article->title(),
            'description' => $article->description(),
            'body' => $article->body(),
            'slug' => $article->slug,
        ]);

        $this->syncTags($eloquentArticle, ...$article->tagList());

        $eloquentArticle->save();

        return $eloquentArticle;
    }

    private function syncTags(EloquentArticle $article, string ...$tags): void
    {
        $tagsIds = [];

        foreach ($tags as $tag) {
            $tagsIds[] = Tag::firstOrCreate(['name' => $tag])->id;
        }

        $article->tags()->sync($tagsIds);
    }}
