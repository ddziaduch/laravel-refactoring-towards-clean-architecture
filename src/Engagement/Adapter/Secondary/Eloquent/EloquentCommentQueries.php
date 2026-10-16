<?php

namespace Conduit\Engagement\Adapter\Secondary\Eloquent;

use Conduit\Engagement\Application\Port\Secondary\CommentQueries;
use Conduit\Engagement\Application\Port\Secondary\CommentRecord;

final class EloquentCommentQueries implements CommentQueries
{
    public function ofArticle(int $articleId): array
    {
        return CommentModel::query()
            ->where('article_id', $articleId)
            ->orderBy('id')
            ->get()
            ->map($this->toRecord(...))
            ->all();
    }

    public function byId(int $commentId): ?CommentRecord
    {
        $model = CommentModel::query()->find($commentId);

        return $model === null ? null : $this->toRecord($model);
    }

    private function toRecord(CommentModel $model): CommentRecord
    {
        return new CommentRecord(
            $model->getKey(),
            $model->created_at->toDateTimeImmutable(),
            $model->updated_at->toDateTimeImmutable(),
            $model->body,
            $model->user_id,
        );
    }
}
