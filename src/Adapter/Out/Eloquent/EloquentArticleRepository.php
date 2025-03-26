<?php

declare(strict_types = 1);

namespace Clean\Adapter\Out\Eloquent;

use App\Models\Article as EloquentArticle;
use Clean\Application\Exception\ArticleDoesNotExist;
use Clean\Application\Port\Out\ArticleRepository;
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
            (int) $eloquentArticle->user_id,
        );
    }

    public function save(DomainArticle $article): void
    {
        try {
            $eloquentArticle = EloquentArticle::where('slug', $article->slug())->firstOrFail();
        } catch (ModelNotFoundException $exception) {
            throw new \LogicException(sprintf('Expected the article with slug %s to exist on this stage', $article->slug()));
        }

        assert($eloquentArticle instanceof EloquentArticle);
        $eloquentArticle->update(['is_removed' => $article->isRemoved()]);
    }
}
