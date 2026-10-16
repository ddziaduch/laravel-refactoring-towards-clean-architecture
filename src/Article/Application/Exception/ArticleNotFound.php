<?php

namespace Conduit\Article\Application\Exception;

use RuntimeException;

final class ArticleNotFound extends RuntimeException
{
    public static function withSlug(string $slug): self
    {
        return new self(sprintf('Article "%s" not found.', $slug));
    }
}
