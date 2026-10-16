<?php

namespace Conduit\Engagement\Application\Port\Secondary;

interface CommentQueries
{
    /**
     * @return list<CommentRecord> oldest first
     */
    public function ofArticle(int $articleId): array;

    public function byId(int $commentId): ?CommentRecord;
}
