<?php

declare(strict_types = 1);

namespace Clean\Adapter\Out\Eloquent;

use App\Models\Article as EloquentArticle;
use App\Models\Tag;
use App\Models\User;
use Clean\Application\Exception\ArticleDoesNotExist;
use Clean\Application\Port\Out\ArticleRepository;
use Clean\Domain\Entity\Article;
use Clean\Domain\Entity\Article as DomainArticle;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EloquentArticleRepository implements ArticleRepository
{
    public function getBySlug(string $articleSlug): DomainArticle
    {
        try {
            $eloquentArticle = EloquentArticle::where('slug', $articleSlug)
                ->where('is_removed', false)
                ->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw ArticleDoesNotExist::forSlug($articleSlug);
        }

        assert($eloquentArticle instanceof EloquentArticle);

        return new DomainArticle(
            $eloquentArticle->slug,
            $eloquentArticle->title,
            $eloquentArticle->description,
            $eloquentArticle->body,
            (int) $eloquentArticle->user_id,
            ...$eloquentArticle->tags->map(fn(Tag $tag): string => $tag->name),
        );
    }

    public function save(Article $article): void
    {
        $user = User::where('id', $article->authorId())->firstOrFail();
        assert($user instanceof User);

        $eloquentArticle = EloquentArticle::where([
            'user_id' => $user->id,
            'slug' => $article->slug(),
        ])->first();

        if ($eloquentArticle === null) {
            $eloquentArticle = $user->articles()->create([
                'title' => $article->title(),
                'description' => $article->description(),
                'body' => $article->body(),
                'slug' => $article->slug(),
                'is_removed' => $article->isRemoved(),
            ]);
        } else {
            $eloquentArticle->fill([
                'title' => $article->title(),
                'description' => $article->description(),
                'body' => $article->body(),
                'slug' => $article->slug(),
                'is_removed' => $article->isRemoved(),
            ]);
        }

        $this->syncTags($eloquentArticle, ...$article->tagList());
        $eloquentArticle->save();

        $reflectionArticle = new \ReflectionObject($article);
        $reflectionIdProperty = $reflectionArticle->getProperty('id');
        $reflectionIdProperty->setAccessible(true);
        $reflectionIdProperty->setValue($article, $eloquentArticle->id);
    }

    private function syncTags(EloquentArticle $article, string ...$tags): void
    {
        $tagsIds = [];

        foreach ($tags as $tag) {
            $tagsIds[] = Tag::firstOrCreate(['name' => $tag])->id;
        }

        $article->tags()->sync($tagsIds);
    }
}
