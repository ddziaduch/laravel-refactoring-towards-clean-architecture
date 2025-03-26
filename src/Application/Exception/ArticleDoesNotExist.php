<?php

declare(strict_types = 1);

namespace Clean\Application\Exception;

use RuntimeException;

final class ArticleDoesNotExist extends RuntimeException
{
    public static function forSlug(string $slug): self
    {
        return new self(sprintf('Article with slug %s does not exist', $slug));
    }
}
