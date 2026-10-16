<?php

namespace Conduit\Engagement\Application\Service;

use Conduit\Engagement\Application\Port\Secondary\AuthorProfiles;
use Conduit\Engagement\Application\Port\Secondary\CommentRecord;
use Conduit\Engagement\Application\ReadModel\CommentReadModel;

final readonly class CommentAssembler
{
    public function __construct(private AuthorProfiles $authorProfiles) {}

    /**
     * @param  list<CommentRecord>  $records
     * @return list<CommentReadModel>
     */
    public function assemble(array $records, ?int $viewerId): array
    {
        $authors = $this->authorProfiles->of(
            array_values(array_unique(array_map(fn (CommentRecord $record): int => $record->authorId, $records))),
            $viewerId,
        );

        return array_map(
            fn (CommentRecord $record): CommentReadModel => new CommentReadModel(
                $record->id,
                $record->createdAt,
                $record->updatedAt,
                $record->body,
                $authors[$record->authorId],
            ),
            $records,
        );
    }
}
