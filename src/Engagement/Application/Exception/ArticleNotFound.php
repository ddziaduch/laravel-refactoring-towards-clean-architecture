<?php

namespace Conduit\Engagement\Application\Exception;

use RuntimeException;

/**
 * The module's own exception: it does not reuse the Article module's one,
 * because that class is not part of Article's public API.
 */
final class ArticleNotFound extends RuntimeException
{
    public static function withSlug(string $slug): self
    {
        return new self(sprintf('Article "%s" not found.', $slug));
    }
}
