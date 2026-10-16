<?php

namespace Conduit\Article\Application\Service;

use Conduit\Article\Application\Port\Secondary\ArticlePage;
use Conduit\Article\Application\Port\Secondary\ArticleRecord;
use Conduit\Article\Application\Port\Secondary\AuthorProfiles;
use Conduit\Article\Application\ReadModel\ArticleList;
use Conduit\Article\Application\ReadModel\ArticleReadModel;
use Conduit\Article\Application\ReadModel\AuthorReadModel;

/**
 * Joins the module's own article records with author profiles provided from
 * outside. One batch call per page, never one call per article.
 */
final readonly class ArticleAssembler
{
    public function __construct(private AuthorProfiles $authorProfiles) {}

    public function list(ArticlePage $page, ?int $viewerId): ArticleList
    {
        $authors = $this->authorProfiles->of(
            array_values(array_unique(array_map(
                fn (ArticleRecord $record): int => $record->authorId,
                $page->records,
            ))),
            $viewerId,
        );

        return new ArticleList(
            array_map(
                fn (ArticleRecord $record): ArticleReadModel => $this->assemble($record, $authors[$record->authorId]),
                $page->records,
            ),
            $page->total,
        );
    }

    public function one(ArticleRecord $record, ?int $viewerId): ArticleReadModel
    {
        return $this->assemble($record, $this->authorProfiles->of([$record->authorId], $viewerId)[$record->authorId]);
    }

    private function assemble(ArticleRecord $record, AuthorReadModel $author): ArticleReadModel
    {
        return new ArticleReadModel(
            $record->slug,
            $record->title,
            $record->description,
            $record->body,
            $record->tagList,
            $record->createdAt,
            $record->updatedAt,
            $record->favorited,
            $record->favoritesCount,
            $author,
        );
    }
}
