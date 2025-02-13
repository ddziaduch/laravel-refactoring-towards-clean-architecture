<?php

declare(strict_types = 1);

namespace Clean\Application\Exception;

use RuntimeException;

final class ArticleDoesNotExist extends RuntimeException
{
    public static function forAuthorIdAndSlug(int $authorId, string $slug): self
    {
        return new self(
            sprintf(
                'User with ID %d does not have an article with slug %s',
                $authorId,
                $slug,
            ),
        );
    }
}
