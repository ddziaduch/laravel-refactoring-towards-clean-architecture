<?php

namespace Conduit\Article\Application\UseCase;

use Conduit\Article\Application\Exception\ArticleNotFound;
use Conduit\Article\Application\Port\Primary\ArticleChanges;
use Conduit\Article\Application\Port\Primary\UpdateArticle;
use Conduit\Article\Application\Port\Secondary\ArticleRepository;
use Conduit\Article\Application\Service\UniqueSlug;
use Conduit\Article\Domain\TagList;

final readonly class UpdateArticleUseCase implements UpdateArticle
{
    public function __construct(
        private ArticleRepository $articles,
        private UniqueSlug $uniqueSlug,
    ) {}

    public function __invoke(string $slug, int $editorId, ArticleChanges $changes): string
    {
        $article = $this->articles->findBySlug($slug) ?? throw ArticleNotFound::withSlug($slug);
        $article->assertAuthoredBy($editorId);

        $edited = $article->edit(
            $editorId,
            title: $changes->title,
            slug: $changes->title === null ? null : $this->uniqueSlug->for($changes->title, $article->id),
            description: $changes->description,
            body: $changes->body,
            tags: $changes->tagList === null ? null : new TagList($changes->tagList),
        );

        $this->articles->save($edited);

        return $edited->slug;
    }
}
