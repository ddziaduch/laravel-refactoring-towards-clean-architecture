<?php

declare(strict_types=1);

namespace Clean\Domain\Entity;

use Clean\Domain\Exception\CommentIsTooShort;

final class Comment
{
    public function __construct(
        public string $articleSlug,
        public string $body,
        public int $authorId,
    ) {
        if (strlen($body) < 10) {
            throw new CommentIsTooShort();
        }
    }
}
