<?php

namespace Conduit\Article\Application\UseCase;

use Conduit\Article\Application\Port\Primary\PublishArticle;
use Conduit\Article\Application\Port\Secondary\ArticleRepository;
use Conduit\Article\Application\Service\UniqueSlug;
use Conduit\Article\Domain\Article;
use Conduit\Article\Domain\TagList;

final readonly class PublishArticleUseCase implements PublishArticle
{
    public function __construct(
        private ArticleRepository $articles,
        private UniqueSlug $uniqueSlug,
    ) {}

    public function __invoke(int $authorId, string $title, string $description, string $body, array $tagList): string
    {
        $article = Article::publish(
            $authorId,
            $this->uniqueSlug->for($title),
            $title,
            $description,
            $body,
            new TagList($tagList),
        );

        $this->articles->add($article);

        return $article->slug;
    }
}
